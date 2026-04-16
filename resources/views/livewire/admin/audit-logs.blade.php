<div>
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Audit Logs</h2>
        <p class="text-sm text-gray-500">Track all system changes and activities</p>
    </div>

    <!-- Filters -->
    <div class="mb-4 flex flex-col gap-3 sm:flex-row">
        <div class="flex-1">
            <input wire:model.live.debounce.300ms="search"
                   type="text"
                   placeholder="Search by user..."
                   class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
        </div>
        <select wire:model.live="filterAction"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
            <option value="">All Actions</option>
            @foreach($actions as $action)
                <option value="{{ $action }}">{{ str_replace('_', ' ', ucfirst($action)) }}</option>
            @endforeach
        </select>
    </div>

    <!-- Logs Table -->
    <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Timestamp</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">User</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Target</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="transition hover:bg-gray-50" wire:key="log-{{ $log->id }}">
                    <td class="px-6 py-3 text-sm text-gray-500">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                    <td class="px-6 py-3 text-sm text-gray-900">{{ $log->user?->name ?? 'System' }}</td>
                    <td class="px-6 py-3">
                        <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                            {{ str_replace('_', ' ', ucfirst($log->action)) }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-sm text-gray-500">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</td>
                    <td class="px-6 py-3 text-xs text-gray-400 font-mono">{{ $log->ip_address }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No audit logs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
        <div class="border-t border-gray-200 px-6 py-3">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
