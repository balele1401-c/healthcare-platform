@extends('layouts.staff')

@section('title', 'Operational Activity Logs')
@section('page_title', 'Clinic Operational Activity Trail')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Operational Audit Trail</h2>
            <p class="text-xs text-slate-500">Record of appointment coordination, front-desk patient intake, and system activities</p>
        </div>

        <form method="GET" action="{{ route('staff.activity.index') }}" class="w-full sm:w-64">
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search action, user..."
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] font-semibold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-6">Timestamp</th>
                        <th class="py-3.5 px-6">Actor</th>
                        <th class="py-3.5 px-6">Action</th>
                        <th class="py-3.5 px-6">Entity</th>
                        <th class="py-3.5 px-6">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($activities as $act)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-6 text-xs text-slate-500 font-mono whitespace-nowrap">
                                {{ $act->created_at ? $act->created_at->format('M d, Y H:i:s') : 'N/A' }}
                            </td>
                            <td class="py-3.5 px-6">
                                <div class="font-semibold text-slate-900 text-xs">{{ $act->user->name ?? 'System' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $act->user->email ?? 'Automated' }}</div>
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="font-mono font-bold text-xs bg-slate-100 text-slate-800 px-2 py-0.5 rounded">
                                    {{ $act->action }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-700 font-medium">
                                {{ $act->entity_type }} #{{ $act->entity_id }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-400 font-mono">
                                {{ $act->ip_address ?? '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-xs">
                                No activity logs recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($activities->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
