@extends('layouts.admin')

@section('title', 'Admin Notifications')
@section('page_title', 'System Alerts & Notifications')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
        <div>
            <h2 class="text-base font-semibold text-slate-900">Notifications & Alerts</h2>
            <p class="text-xs text-slate-500">Platform operational notifications, booking reminders, and emergency signals</p>
        </div>

        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl text-xs transition-colors">
                    Mark All as Read ({{ $unreadCount }})
                </button>
            </form>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden divide-y divide-slate-100">
        @forelse ($notifications as $notif)
            <div class="p-5 flex items-start gap-4 hover:bg-slate-50 transition-colors {{ $notif->read_at === null ? 'bg-teal-50/20' : '' }}">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 {{ $notif->read_at === null ? 'bg-teal-600 text-white' : 'bg-slate-100 text-slate-500' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-4">
                        <h4 class="text-sm font-semibold text-slate-900 truncate">{{ $notif->title }}</h4>
                        <span class="text-[11px] text-slate-400 whitespace-nowrap">{{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $notif->message }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-slate-100 text-slate-600">
                            {{ str_replace('_', ' ', $notif->notification_type ?? 'general') }}
                        </span>
                        @if ($notif->read_at === null)
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-teal-50 text-teal-700">Unread</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-slate-400 text-xs">
                No notifications logged.
            </div>
        @endforelse
    </div>

    @if ($notifications->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
