@extends('layouts.staff')

@section('title', 'Staff Notifications')
@section('page_title', 'Clinic Notifications & Operational Alerts')

@section('content')
<div class="space-y-6 max-w-4xl">
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h2 class="text-base font-semibold text-slate-900">Notifications</h2>
            @if ($unreadCount > 0)
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-100 text-cyan-800">
                    {{ $unreadCount }} Unread
                </span>
            @endif
        </div>

        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('staff.notifications.read-all') }}">
                @csrf
                <button type="submit" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700">
                    Mark all as read
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs divide-y divide-slate-100 overflow-hidden">
        @forelse ($notifications as $note)
            <div class="p-4 sm:p-5 flex items-start gap-4 hover:bg-slate-50 transition-colors {{ is_null($note->read_at) ? 'bg-cyan-50/40' : '' }}">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ is_null($note->read_at) ? 'bg-cyan-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-baseline justify-between gap-2">
                        <h4 class="text-xs font-bold text-slate-900 truncate">{{ $note->title }}</h4>
                        <span class="text-[11px] text-slate-400 font-mono whitespace-nowrap">{{ $note->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $note->message }}</p>
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-slate-400 text-xs">
                No notifications received.
            </div>
        @endforelse
    </div>

    @if ($notifications->hasPages())
        <div>
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
