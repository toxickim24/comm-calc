<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogs extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterAction = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = AuditLog::query()
            ->with('user')
            ->when($this->search, fn($q) => $q->whereHas('user', fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
            ))
            ->when($this->filterAction, fn($q) => $q->where('action', $this->filterAction))
            ->orderByDesc('created_at')
            ->paginate(25);

        $actions = AuditLog::distinct()->pluck('action');

        return view('livewire.admin.audit-logs', compact('logs', 'actions'))
            ->layout('layouts.app')
            ->title('Audit Logs');
    }
}
