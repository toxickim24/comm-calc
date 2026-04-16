<?php

namespace Tests\Unit;

use App\Services\SpiffCalculatorService;
use PHPUnit\Framework\TestCase;

class SpiffCalculatorServiceTest extends TestCase
{
    protected array $settings;

    protected function setUp(): void
    {
        parent::setUp();

        $this->settings = [
            'improvement_bonus' => 500,
            'improvement_min_appts' => 10,
            'improvement_min_points' => 5,
            'target_20_bonus' => 500,
            'target_30_bonus' => 1000,
            'target_min_appts' => 12,
            'fast_close_per_deal' => 250,
            'highest_close_rate_bonus' => 500,
            'tie_handling' => 1,
        ];
    }

    public function test_improvement_bonus_eligible(): void
    {
        $service = new SpiffCalculatorService($this->settings);
        $method = new \ReflectionMethod($service, 'calcImprovementBonus');

        $result = $method->invoke($service, 6, 12); // 6 points, 12 appts
        $this->assertEquals(500, $result);
    }

    public function test_improvement_bonus_not_enough_points(): void
    {
        $service = new SpiffCalculatorService($this->settings);
        $method = new \ReflectionMethod($service, 'calcImprovementBonus');

        $result = $method->invoke($service, 3, 12); // only 3 points
        $this->assertEquals(0, $result);
    }

    public function test_improvement_bonus_not_enough_appointments(): void
    {
        $service = new SpiffCalculatorService($this->settings);
        $method = new \ReflectionMethod($service, 'calcImprovementBonus');

        $result = $method->invoke($service, 6, 8); // only 8 appts
        $this->assertEquals(0, $result);
    }

    public function test_target_bonus_30_percent(): void
    {
        $service = new SpiffCalculatorService($this->settings);
        $method = new \ReflectionMethod($service, 'calcTargetBonus');

        $result = $method->invoke($service, 32, 15); // 32% rate, 15 appts
        $this->assertEquals(1000, $result);
    }

    public function test_target_bonus_20_percent(): void
    {
        $service = new SpiffCalculatorService($this->settings);
        $method = new \ReflectionMethod($service, 'calcTargetBonus');

        $result = $method->invoke($service, 22, 14); // 22% rate, 14 appts (meets 12 min)
        $this->assertEquals(500, $result);
    }

    public function test_target_bonus_20_percent_not_enough_appointments(): void
    {
        $service = new SpiffCalculatorService($this->settings);
        $method = new \ReflectionMethod($service, 'calcTargetBonus');

        // 22% rate but only 8 appts — both tiers need 12+
        $result = $method->invoke($service, 22, 8);
        $this->assertEquals(0, $result);
    }

    public function test_target_bonus_30_needs_min_appointments(): void
    {
        $service = new SpiffCalculatorService($this->settings);
        $method = new \ReflectionMethod($service, 'calcTargetBonus');

        // 30%+ rate but only 10 appts (needs 12 for both tiers)
        $result = $method->invoke($service, 35, 10);
        $this->assertEquals(0, $result);
    }

    public function test_target_bonus_below_20(): void
    {
        $service = new SpiffCalculatorService($this->settings);
        $method = new \ReflectionMethod($service, 'calcTargetBonus');

        $result = $method->invoke($service, 15, 15);
        $this->assertEquals(0, $result);
    }

    public function test_fast_close_bonus(): void
    {
        $service = new SpiffCalculatorService($this->settings);
        $method = new \ReflectionMethod($service, 'calcFastCloseBonus');

        $result = $method->invoke($service, 3);
        $this->assertEquals(750, $result); // 3 * $250
    }

    public function test_fast_close_bonus_zero(): void
    {
        $service = new SpiffCalculatorService($this->settings);
        $method = new \ReflectionMethod($service, 'calcFastCloseBonus');

        $result = $method->invoke($service, 0);
        $this->assertEquals(0, $result);
    }
}
