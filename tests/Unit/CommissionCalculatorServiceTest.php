<?php

namespace Tests\Unit;

use App\Services\CommissionCalculatorService;
use PHPUnit\Framework\TestCase;

class CommissionCalculatorServiceTest extends TestCase
{
    protected CommissionCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Use default seeded settings
        $this->service = new CommissionCalculatorService([
            'min_gm_percent' => 35,
            'target_gm_percent' => 47,
            'floor_min_amount' => 750,
            'floor_percent' => 0.5,
            'tier_35_1_37_9_rate' => 3,
            'tier_38_40_9_rate' => 5,
            'tier_41_43_9_rate' => 7,
            'tier_44_46_9_rate' => 9,
            'tier_47_rate' => 10,
            'surplus_multiplier' => 0.5,
            'fast_close_spiff' => 250,
            'fast_close_days' => 3,
        ]);
    }

    public function test_below_floor_returns_zero(): void
    {
        $result = $this->service->calculate(25000, 34);

        $this->assertEquals(0, $result['total_payout']);
        $this->assertEquals('Below Floor', $result['tier']);
    }

    public function test_floor_gm_gets_minimum_750(): void
    {
        $result = $this->service->calculate(10000, 35);

        $this->assertGreaterThanOrEqual(750, $result['base_commission']);
        $this->assertEquals('Floor (35%)', $result['tier']);
    }

    public function test_floor_gm_large_contract(): void
    {
        // $200,000 * 0.5% = $1000, which exceeds $750 floor
        $result = $this->service->calculate(200000, 35);

        $this->assertEquals(1000, $result['base_commission']);
    }

    public function test_tier_35_1_to_37_9(): void
    {
        $result = $this->service->calculate(25000, 36);

        $this->assertEquals(750, $result['base_commission']); // 25000 * 3%
        $this->assertEquals('35.1% – 37.9%', $result['tier']);
        $this->assertEquals(3, $result['tier_rate']);
    }

    public function test_tier_38_to_40_9(): void
    {
        $result = $this->service->calculate(25000, 40);

        $this->assertEquals(1250, $result['base_commission']); // 25000 * 5%
        $this->assertEquals('38% – 40.9%', $result['tier']);
    }

    public function test_tier_41_to_43_9(): void
    {
        $result = $this->service->calculate(25000, 42);

        $this->assertEquals(1750, $result['base_commission']); // 25000 * 7%
        $this->assertEquals('41% – 43.9%', $result['tier']);
    }

    public function test_tier_44_to_46_9(): void
    {
        $result = $this->service->calculate(25000, 45);

        $this->assertEquals(2250, $result['base_commission']); // 25000 * 9%
        $this->assertEquals('44% – 46.9%', $result['tier']);
    }

    public function test_tier_47_plus(): void
    {
        $result = $this->service->calculate(25000, 47);

        $this->assertEquals(2500, $result['base_commission']); // 25000 * 10%
        $this->assertEquals('47%+', $result['tier']);
    }

    public function test_surplus_bonus_above_target(): void
    {
        // 50% GM on $25,000: 3% surplus * 0.5 multiplier
        $result = $this->service->calculate(25000, 50);

        $this->assertEquals(2500, $result['base_commission']); // 10%
        $this->assertEquals(375, $result['surplus_bonus']); // 25000 * 0.03 * 0.5
    }

    public function test_no_surplus_at_target(): void
    {
        $result = $this->service->calculate(25000, 47);

        $this->assertEquals(0, $result['surplus_bonus']);
    }

    public function test_fast_close_bonus(): void
    {
        $result = $this->service->calculate(25000, 42, true);

        $this->assertEquals(250, $result['fast_close_bonus']);
        $this->assertTrue($result['is_fast_close']);
    }

    public function test_no_fast_close_bonus(): void
    {
        $result = $this->service->calculate(25000, 42, false);

        $this->assertEquals(0, $result['fast_close_bonus']);
        $this->assertFalse($result['is_fast_close']);
    }

    public function test_total_payout_calculation(): void
    {
        // 50% GM, fast close: base (2500) + surplus (375) + fast close (250) = 3125
        $result = $this->service->calculate(25000, 50, true);

        $this->assertEquals(3125, $result['total_payout']);
    }

    public function test_result_includes_settings_snapshot(): void
    {
        $result = $this->service->calculate(25000, 42);

        $this->assertArrayHasKey('settings_snapshot', $result);
        $this->assertIsArray($result['settings_snapshot']);
        $this->assertArrayHasKey('min_gm_percent', $result['settings_snapshot']);
    }

    public function test_zero_contract_value(): void
    {
        $result = $this->service->calculate(0, 42);

        $this->assertEquals(0, $result['base_commission']);
        $this->assertEquals(0, $result['total_payout']);
        $this->assertEquals('Below Floor', $result['tier']);
    }

    public function test_zero_contract_at_35_does_not_get_floor_750(): void
    {
        $result = $this->service->calculate(0, 35);

        $this->assertEquals(0, $result['total_payout']);
        $this->assertEquals('Below Floor', $result['tier']);
    }

    public function test_gm_34_99_returns_zero(): void
    {
        $result = $this->service->calculate(25000, 34.99);

        $this->assertEquals(0, $result['total_payout']);
        $this->assertEquals('Below Floor', $result['tier']);
    }

    public function test_gm_exactly_47_no_surplus(): void
    {
        $result = $this->service->calculate(25000, 47);

        $this->assertEquals(2500, $result['base_commission']);
        $this->assertEquals(0, $result['surplus_bonus']);
        $this->assertEquals(2500, $result['total_payout']);
    }

    public function test_gm_47_1_gets_surplus(): void
    {
        // 47.1%: base = 10% of 25000 = 2500
        // surplus = 0.5 * ((47.1 - 47) / 100 * 25000) = 0.5 * (0.001 * 25000) = 0.5 * 25 = 12.5
        $result = $this->service->calculate(25000, 47.1);

        $this->assertEquals(2500, $result['base_commission']);
        $this->assertEquals(12.5, $result['surplus_bonus']);
        $this->assertEquals(2512.5, $result['total_payout']);
    }

    public function test_negative_contract_value(): void
    {
        $result = $this->service->calculate(-5000, 42);

        $this->assertEquals(0, $result['total_payout']);
        $this->assertEquals('Below Floor', $result['tier']);
    }
}
