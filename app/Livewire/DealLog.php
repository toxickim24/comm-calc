<?php

namespace App\Livewire;

use App\Enums\DealStatus;
use App\Models\AuditLog;
use App\Models\CommissionPayout;
use App\Models\CommissionSetting;
use App\Models\Deal;
use App\Models\MonthlySnapshot;
use App\Models\User;
use App\Services\CommissionCalculatorService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class DealLog extends Component
{
    use WithPagination;

    // Modal state
    public bool $showModal = false;
    public bool $editing = false;
    public ?int $editingId = null;

    // Form fields
    public string $user_id = '';
    public string $month = '';
    public string $client_name = '';
    public string $appointment_date = '';
    public string $contract_signed_date = '';
    public string $deposit_date = '';
    public string $original_contract_price = '';
    public string $sold_contract_value = '';
    public string $estimated_gm_percent = '';
    public string $deal_status = 'lead';
    public string $notes = '';

    // Filters
    public string $search = '';
    public string $filterMonth = '';
    public string $filterStatus = '';
    public string $filterRep = '';

    // Batch selection
    public array $selectedDeals = [];
    public string $batchAction = '';

    public function mount(): void
    {
        $this->filterMonth = now()->format('Y-m');
    }

    protected function rules(): array
    {
        return [
            'client_name' => 'required|min:2|max:255',
            'sold_contract_value' => 'required|numeric|gt:0',
            'estimated_gm_percent' => 'required|numeric|min:0|max:100',
            'deal_status' => 'required|in:' . implode(',', array_column(DealStatus::cases(), 'value')),
            'month' => 'required|date_format:Y-m',
            'appointment_date' => 'nullable|date',
            'contract_signed_date' => 'nullable|date',
            'deposit_date' => 'nullable|date',
            'original_contract_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|max:1000',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'client_name' => 'client name',
            'sold_contract_value' => 'contract value',
            'estimated_gm_percent' => 'gross margin %',
            'deal_status' => 'status',
            'appointment_date' => 'appointment date',
            'contract_signed_date' => 'contract signed date',
            'deposit_date' => 'deposit date',
            'original_contract_price' => 'original price',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->month = now()->format('Y-m');

        // Default to current user for sales reps
        if (auth()->user()->isSalesRep()) {
            $this->user_id = (string) auth()->id();
        }

        $this->showModal = true;
        $this->editing = false;
    }

    public function edit(int $id): void
    {
        $deal = $this->findDeal($id);
        if (!$deal) return;

        $this->editingId = $deal->id;
        $this->user_id = (string) $deal->user_id;
        $this->month = $deal->month->format('Y-m');
        $this->client_name = $deal->client_name;
        $this->appointment_date = $deal->appointment_date?->format('Y-m-d') ?? '';
        $this->contract_signed_date = $deal->contract_signed_date?->format('Y-m-d') ?? '';
        $this->deposit_date = $deal->deposit_date?->format('Y-m-d') ?? '';
        $this->original_contract_price = $deal->original_contract_price ?? '';
        $this->sold_contract_value = $deal->sold_contract_value;
        $this->estimated_gm_percent = $deal->estimated_gm_percent;
        $this->deal_status = $deal->deal_status->value;
        $this->notes = $deal->notes ?? '';
        $this->editing = true;
        $this->showModal = true;
    }

    protected function isMonthLocked(string $month): bool
    {
        return MonthlySnapshot::isMonthLocked($month . '-01');
    }

    public function save(): void
    {
        $this->validate();

        // Closed Won requires both dates
        if ($this->deal_status === 'closed_won') {
            if (!$this->appointment_date || !$this->contract_signed_date) {
                $this->dispatch('toast', type: 'error', message: 'Closed Won requires both Appointment Date and Contract Signed Date.');
                return;
            }
        }

        // Check if month is locked
        if ($this->isMonthLocked($this->month)) {
            $this->dispatch('toast', type: 'error', message: 'This month is locked. Deals cannot be created or modified.');
            return;
        }

        // Sales reps can only create deals for themselves
        $userId = auth()->user()->isSalesRep()
            ? auth()->id()
            : (int) $this->user_id;

        if (!$userId) {
            $this->dispatch('toast', type: 'error', message: 'Please select a sales rep.');
            return;
        }

        // Calculate days to close and fast close flag
        $daysToClose = $this->calculateDaysToClose();
        $isFastClose = $this->detectFastClose($daysToClose);

        $data = [
            'user_id' => $userId,
            'month' => $this->month . '-01',
            'client_name' => $this->client_name,
            'appointment_date' => $this->appointment_date ?: null,
            'contract_signed_date' => $this->contract_signed_date ?: null,
            'deposit_date' => $this->deposit_date ?: null,
            'original_contract_price' => $this->original_contract_price ?: null,
            'sold_contract_value' => $this->sold_contract_value,
            'estimated_gm_percent' => $this->estimated_gm_percent,
            'deal_status' => $this->deal_status,
            'days_to_close' => $daysToClose,
            'is_fast_close' => $isFastClose,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editing) {
            $deal = $this->findDeal($this->editingId);
            if (!$deal) return;

            $oldValues = $deal->only([
                'client_name', 'sold_contract_value', 'estimated_gm_percent',
                'deal_status', 'appointment_date', 'contract_signed_date',
            ]);
            $oldStatus = $deal->deal_status;

            $deal->update($data);

            AuditLog::record('deal_updated', $deal, $oldValues, $data);

            // If status changed to Closed Won, auto-calculate commission
            if ($oldStatus !== DealStatus::ClosedWon && $deal->deal_status === DealStatus::ClosedWon) {
                $this->calculateCommissionPayout($deal);
            }

            // If status changed away from Closed Won, remove commission payout
            if ($oldStatus === DealStatus::ClosedWon && $deal->deal_status !== DealStatus::ClosedWon) {
                $deal->commissionPayout?->delete();
            }

            $this->dispatch('toast', type: 'success', message: 'Deal updated.');
        } else {
            $deal = Deal::create($data);

            AuditLog::record('deal_created', $deal, null, $data);

            // Auto-calculate commission if created as Closed Won
            if ($deal->deal_status === DealStatus::ClosedWon) {
                $this->calculateCommissionPayout($deal);
            }

            $this->dispatch('toast', type: 'success', message: 'Deal created.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $deal = $this->findDeal($id);
        if (!$deal) return;

        if (MonthlySnapshot::isMonthLocked($deal->month)) {
            $this->dispatch('toast', type: 'error', message: 'This month is locked. Deals cannot be deleted.');
            return;
        }

        AuditLog::record('deal_deleted', $deal, $deal->toArray());

        // Remove associated commission payout
        $deal->commissionPayout?->delete();
        $deal->delete();

        $this->dispatch('toast', type: 'success', message: 'Deal deleted.');
    }

    public function updateStatus(int $id, string $status): void
    {
        $deal = $this->findDeal($id);
        if (!$deal) return;

        if (MonthlySnapshot::isMonthLocked($deal->month)) {
            $this->dispatch('toast', type: 'error', message: 'This month is locked. Deal status cannot be changed.');
            return;
        }

        $newStatus = DealStatus::from($status);
        $oldStatus = $deal->deal_status;

        if ($oldStatus === $newStatus) return;

        // Closed Won requires both dates
        if ($newStatus === DealStatus::ClosedWon) {
            if (!$deal->appointment_date || !$deal->contract_signed_date) {
                $this->dispatch('toast', type: 'error', message: 'Cannot mark as Closed Won — Appointment Date and Contract Signed Date are both required. Edit the deal first.');
                return;
            }
        }

        $oldValues = ['deal_status' => $oldStatus->value];
        $deal->update(['deal_status' => $newStatus]);

        AuditLog::record('deal_status_changed', $deal, $oldValues, ['deal_status' => $newStatus->value]);

        // Commission logic on status transitions
        if ($oldStatus !== DealStatus::ClosedWon && $newStatus === DealStatus::ClosedWon) {
            $this->calculateCommissionPayout($deal->fresh());
        } elseif ($oldStatus === DealStatus::ClosedWon && $newStatus !== DealStatus::ClosedWon) {
            $deal->commissionPayout?->delete();
        }

        $this->dispatch('toast', type: 'success', message: "Status changed to {$newStatus->label()}.");
    }

    public function batchUpdateStatus(): void
    {
        if (empty($this->selectedDeals) || !$this->batchAction) {
            $this->dispatch('toast', type: 'error', message: 'Select deals and an action first.');
            return;
        }

        // Block Closed Won in batch — too risky for batch
        if ($this->batchAction === 'closed_won') {
            $this->dispatch('toast', type: 'error', message: 'Closed Won cannot be applied in batch. Change each deal individually.');
            return;
        }

        $count = 0;
        foreach ($this->selectedDeals as $dealId) {
            $deal = $this->findDeal((int) $dealId);
            if (!$deal) continue;

            if (MonthlySnapshot::isMonthLocked($deal->month)) continue;

            $newStatus = DealStatus::from($this->batchAction);
            $oldStatus = $deal->deal_status;
            if ($oldStatus === $newStatus) continue;

            $deal->update(['deal_status' => $newStatus]);
            AuditLog::record('deal_status_changed', $deal, ['deal_status' => $oldStatus->value], ['deal_status' => $newStatus->value]);

            // Remove commission if moving away from Closed Won
            if ($oldStatus === DealStatus::ClosedWon && $newStatus !== DealStatus::ClosedWon) {
                $deal->commissionPayout?->delete();
            }

            $count++;
        }

        $this->selectedDeals = [];
        $this->batchAction = '';
        $this->dispatch('toast', type: 'success', message: "{$count} deal(s) updated.");
    }

    protected function calculateCommissionPayout(Deal $deal): void
    {
        // Remove existing payout if any
        $deal->commissionPayout?->delete();

        $service = new CommissionCalculatorService();
        $result = $service->calculate(
            (float) $deal->sold_contract_value,
            (float) $deal->estimated_gm_percent,
            $deal->is_fast_close
        );

        CommissionPayout::create([
            'deal_id' => $deal->id,
            'user_id' => $deal->user_id,
            'month' => $deal->month,
            'sold_contract_value' => $result['contract_value'],
            'gm_percent' => $result['gm_percent'],
            'tier' => $result['tier'],
            'base_commission' => $result['base_commission'],
            'surplus_bonus' => $result['surplus_bonus'],
            'fast_close_bonus' => $result['fast_close_bonus'],
            'total_payout' => $result['total_payout'],
            'settings_snapshot' => $result['settings_snapshot'],
        ]);
    }

    protected function calculateDaysToClose(): ?int
    {
        if (!$this->appointment_date || !$this->contract_signed_date) {
            return null;
        }

        $appt = Carbon::parse($this->appointment_date);
        $signed = Carbon::parse($this->contract_signed_date);

        // If signed before appointment, return null (invalid data)
        if ($signed->lt($appt)) {
            return null;
        }

        return $appt->diffInDays($signed);
    }

    protected function detectFastClose(?int $daysToClose): bool
    {
        if ($daysToClose === null) return false;

        // Fast close only applies to Closed Won deals
        if ($this->deal_status !== 'closed_won') return false;

        $fastCloseDays = (int) CommissionSetting::getValue('fast_close_days', 3);
        return $daysToClose <= $fastCloseDays;
    }

    protected function findDeal(int $id): ?Deal
    {
        $deal = Deal::find($id);

        if (!$deal) {
            $this->dispatch('toast', type: 'error', message: 'Deal not found.');
            return null;
        }

        // Sales reps can only access their own deals
        if (auth()->user()->isSalesRep() && $deal->user_id !== auth()->id()) {
            $this->dispatch('toast', type: 'error', message: 'Unauthorized.');
            return null;
        }

        return $deal;
    }

    private function resetForm(): void
    {
        $this->reset([
            'editingId', 'editing', 'user_id', 'client_name',
            'appointment_date', 'contract_signed_date', 'deposit_date',
            'original_contract_price', 'sold_contract_value',
            'estimated_gm_percent', 'notes',
        ]);
        $this->deal_status = 'lead';
        $this->month = now()->format('Y-m');
        $this->resetValidation();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterMonth(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFilterRep(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();

        $deals = Deal::query()
            ->with(['user', 'commissionPayout'])
            ->when($user->isSalesRep(), fn($q) => $q->where('user_id', $user->id))
            ->when($this->search, fn($q) => $q->where('client_name', 'like', "%{$this->search}%"))
            ->when($this->filterMonth, fn($q) => $q->whereMonth('month', Carbon::parse($this->filterMonth . '-01')->month)
                ->whereYear('month', Carbon::parse($this->filterMonth . '-01')->year))
            ->when($this->filterStatus, fn($q) => $q->where('deal_status', $this->filterStatus))
            ->when($this->filterRep && !$user->isSalesRep(), fn($q) => $q->where('user_id', $this->filterRep))
            ->orderByDesc('created_at')
            ->paginate(15);

        $salesReps = $user->isSalesRep()
            ? collect()
            : User::where('role', 'sales_rep')->orderBy('name')->get();

        return view('livewire.deal-log', [
            'deals' => $deals,
            'statuses' => DealStatus::cases(),
            'salesReps' => $salesReps,
        ])->layout('layouts.app')->title('Deal Log');
    }
}
