@extends('layouts.admin')

@section('title', 'Security & Audit Trail')
@section('page_title', 'Security & Compliance Audit Trail')

@section('content')
<div class="space-y-6">
    <!-- Header & Filter Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">System Audit Trail</h2>
            <p class="text-xs text-slate-500">Immutable security logs, administrative changes, and authentication events</p>
        </div>

        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="flex flex-wrap items-center gap-2.5">
            <div class="relative flex-1 sm:w-56">
                <input type="text" name="search" value="{{ $search }}" placeholder="Action, user, IP..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <select name="action" class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500">
                <option value="">All Actions</option>
                @foreach ($actions as $act)
                    <option value="{{ $act }}" {{ $action === $act ? 'selected' : '' }}>{{ $act }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-medium transition-colors shadow-xs">
                Filter
            </button>
            @if ($search || $action)
                <a href="{{ route('admin.audit-logs.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Clear</a>
            @endif
        </form>
    </div>

    <!-- Audit Logs Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Timestamp</th>
                        <th class="py-3.5 px-6">Action</th>
                        <th class="py-3.5 px-6">Actor / User</th>
                        <th class="py-3.5 px-6">Entity</th>
                        <th class="py-3.5 px-6">IP Address</th>
                        <th class="py-3.5 px-6">Details / Delta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($auditLogs as $log)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 text-xs text-slate-500 whitespace-nowrap">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="font-mono text-xs font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs">
                                <div class="font-medium text-slate-900">{{ $log->user->name ?? 'System' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $log->user->email ?? 'Automated' }}</div>
                            </td>
                            <td class="py-3.5 px-6 text-xs">
                                <span class="font-medium text-slate-800">{{ $log->entity_type }}</span>
                                @if ($log->entity_id)
                                    <span class="text-slate-400 font-mono text-[11px]">#{{ $log->entity_id }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-xs font-mono text-slate-600">
                                {{ $log->ip_address }}
                            </td>
                            <td class="py-3.5 px-6 text-xs font-mono text-slate-500 max-w-xs truncate">
                                @if ($log->new_data)
                                    {{ json_encode($log->new_data) }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                No audit records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($auditLogs->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
