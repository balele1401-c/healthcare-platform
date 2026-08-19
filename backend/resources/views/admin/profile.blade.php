@extends('layouts.admin')

@section('title', 'Admin Account Profile')
@section('page_title', 'Administrator Profile')

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Profile Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-2xl bg-teal-600 text-white flex items-center justify-center font-bold text-3xl shadow-lg shadow-teal-600/30 flex-shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-slate-900">{{ $user->name }}</h2>
                    <span class="px-3 py-0.5 rounded-full text-xs font-bold bg-teal-50 text-teal-700 border border-teal-200 uppercase tracking-wider">
                        {{ $user->role->label() }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 mt-1">{{ $user->email }} &bull; {{ $user->phone ?? 'No phone recorded' }}</p>
                <div class="flex items-center gap-2 mt-3 text-xs text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Account Active &bull; Verified Administrator</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-700 text-xs font-semibold transition-colors">
                Sign Out
            </button>
        </form>
    </div>

    <!-- Security & Activity Logs -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-4">
        <h3 class="font-semibold text-slate-900 text-base border-b border-slate-100 pb-3">Recent Authentication Events</h3>

        <div class="divide-y divide-slate-100">
            @forelse ($recentLogins as $log)
                <div class="py-3 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-teal-500"></span>
                        <div>
                            <p class="font-semibold text-slate-900 font-mono">{{ $log->action }}</p>
                            <p class="text-slate-500 text-[11px]">IP: {{ $log->ip_address }}</p>
                        </div>
                    </div>
                    <span class="text-slate-400 font-mono">{{ $log->created_at->format('M d, Y H:i') }}</span>
                </div>
            @empty
                <p class="text-xs text-slate-400 py-4 text-center">No recent login events recorded.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
