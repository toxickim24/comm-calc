<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_commission_statement_pdf_download(): void
    {
        $user = User::where('email', 'admin@baysidepavers.com')->first();

        $response = $this->actingAs($user)->get('/export/commission-statement?month=' . now()->format('Y-m'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_spiff_report_pdf_download(): void
    {
        $user = User::where('email', 'admin@baysidepavers.com')->first();

        $response = $this->actingAs($user)->get('/export/spiff-report?month=' . now()->format('Y-m'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_deal_log_excel_download(): void
    {
        $user = User::where('email', 'admin@baysidepavers.com')->first();

        $response = $this->actingAs($user)->get('/export/deal-log?month=' . now()->format('Y-m'));
        $response->assertStatus(200);
    }

    public function test_payout_history_excel_download(): void
    {
        $user = User::where('email', 'admin@baysidepavers.com')->first();

        $response = $this->actingAs($user)->get('/export/payout-history?month=' . now()->format('Y-m'));
        $response->assertStatus(200);
    }

    public function test_guest_cannot_export(): void
    {
        $response = $this->get('/export/commission-statement');
        $response->assertRedirect('/login');
    }
}
