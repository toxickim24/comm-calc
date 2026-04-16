<?php

namespace Tests\Feature;

use App\Enums\DealStatus;
use App\Models\Deal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DealTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_deal_log_page_renders(): void
    {
        $user = User::where('email', 'admin@baysidepavers.com')->first();

        $response = $this->actingAs($user)->get('/deals');
        $response->assertStatus(200);
    }

    public function test_sales_rep_can_access_deals(): void
    {
        $user = User::where('email', 'john@baysidepavers.com')->first();

        $response = $this->actingAs($user)->get('/deals');
        $response->assertStatus(200);
    }

    public function test_create_deal(): void
    {
        $rep = User::where('email', 'john@baysidepavers.com')->first();

        Livewire::actingAs($rep)
            ->test(\App\Livewire\DealLog::class)
            ->call('create')
            ->set('client_name', 'Test Client')
            ->set('sold_contract_value', '25000')
            ->set('estimated_gm_percent', '42')
            ->set('month', now()->format('Y-m'))
            ->set('deal_status', 'lead')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('deals', [
            'client_name' => 'Test Client',
            'user_id' => $rep->id,
        ]);
    }

    public function test_deal_status_transition_creates_commission(): void
    {
        $rep = User::where('email', 'john@baysidepavers.com')->first();

        $deal = Deal::create([
            'user_id' => $rep->id,
            'month' => now()->format('Y-m-01'),
            'client_name' => 'Commission Test',
            'appointment_date' => now()->subDays(5),
            'sold_contract_value' => 25000,
            'estimated_gm_percent' => 42,
            'deal_status' => DealStatus::QuoteSent,
        ]);

        Livewire::actingAs($rep)
            ->test(\App\Livewire\DealLog::class)
            ->call('updateStatus', $deal->id, 'closed_won');

        $this->assertDatabaseHas('commission_payouts', [
            'deal_id' => $deal->id,
            'user_id' => $rep->id,
        ]);
    }

    public function test_delete_deal_soft_deletes(): void
    {
        $admin = User::where('email', 'admin@baysidepavers.com')->first();
        $rep = User::where('email', 'john@baysidepavers.com')->first();

        $deal = Deal::create([
            'user_id' => $rep->id,
            'month' => now()->format('Y-m-01'),
            'client_name' => 'Delete Test',
            'appointment_date' => now(),
            'sold_contract_value' => 10000,
            'estimated_gm_percent' => 40,
            'deal_status' => DealStatus::Lead,
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\DealLog::class)
            ->call('delete', $deal->id);

        $this->assertSoftDeleted('deals', ['id' => $deal->id]);
    }

    public function test_sales_rep_cannot_see_other_reps_deals(): void
    {
        $john = User::where('email', 'john@baysidepavers.com')->first();
        $jane = User::where('email', 'jane@baysidepavers.com')->first();

        Deal::create([
            'user_id' => $jane->id,
            'month' => now()->format('Y-m-01'),
            'client_name' => 'Jane Deal',
            'appointment_date' => now(),
            'sold_contract_value' => 15000,
            'estimated_gm_percent' => 38,
            'deal_status' => DealStatus::Lead,
        ]);

        Livewire::actingAs($john)
            ->test(\App\Livewire\DealLog::class)
            ->assertDontSee('Jane Deal');
    }
}
