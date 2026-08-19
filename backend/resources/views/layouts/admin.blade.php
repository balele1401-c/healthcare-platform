<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') — HealthCare Medical Platform</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="h-full flex flex-col antialiased text-slate-800">
    <div class="min-h-full flex">
        <!-- Desktop Sidebar -->
        <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-slate-900 border-r border-slate-800 z-30">
            <!-- Brand Logo -->
            <div class="h-16 flex items-center px-6 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-teal-600 flex items-center justify-center text-white shadow-md shadow-teal-900/40">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <span class="font-bold text-white tracking-tight text-base">HealthCare</span>
                        <span class="block text-[10px] uppercase font-semibold text-teal-400 tracking-wider">Admin Portal</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto" aria-label="Sidebar">
                <div class="px-2 pb-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Clinical Operations</div>

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.patients.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.patients*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Patients
                </a>

                <a href="{{ route('admin.doctors.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.doctors*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Doctors
                </a>

                <a href="{{ route('admin.specialties.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.specialties*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    Specialties
                </a>

                <a href="{{ route('admin.appointments.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.appointments*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Appointments
                </a>

                <div class="pt-4 px-2 pb-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Clinical Records</div>

                <a href="{{ route('admin.medical-records.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.medical-records*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Medical Records
                </a>

                <a href="{{ route('admin.prescriptions.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.prescriptions*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    Prescriptions
                </a>

                <div class="pt-4 px-2 pb-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Governance & Finance</div>

                <a href="{{ route('admin.payments.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.payments*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    Payments
                </a>

                <a href="{{ route('admin.notifications.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.notifications*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Notifications
                </a>

                <a href="{{ route('admin.audit-logs.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.audit-logs*') ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Audit Trail
                </a>
            </nav>

            <!-- User Info & Logout Footer -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/60 flex items-center justify-between">
                <a href="{{ route('admin.profile') }}" class="flex items-center gap-3 min-w-0 hover:opacity-80 transition-opacity">
                    <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center text-teal-400 font-semibold text-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="truncate">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</p>
                    </div>
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" title="Logout"
                            class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="lg:pl-64 flex flex-col flex-1 min-w-0">
            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-20 flex items-center justify-between px-4 sm:px-6 lg:px-8">
                <!-- Mobile Menu Button & Title -->
                <div class="flex items-center gap-3">
                    <button type="button" id="mobile-menu-btn"
                            class="lg:hidden p-2 rounded-md text-slate-600 hover:text-slate-900 hover:bg-slate-100">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-lg sm:text-xl font-semibold text-slate-900 leading-tight">@yield('page_title', 'Dashboard')</h1>
                    </div>
                </div>

                <!-- Right Quick Controls -->
                <div class="flex items-center gap-3">
                    <!-- Environment Pill -->
                    <div class="hidden sm:flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Platform Healthy
                    </div>

                    <!-- Quick Notifications Link -->
                    <a href="{{ route('admin.notifications.index') }}" class="p-2 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors relative">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </a>

                    <!-- Profile Dropdown Link -->
                    <a href="{{ route('admin.profile') }}" class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition-colors text-sm font-medium text-slate-700">
                        <div class="w-6 h-6 rounded-full bg-teal-100 text-teal-700 flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="hidden md:inline">{{ Auth::user()->name ?? 'Admin' }}</span>
                    </a>
                </div>
            </header>

            <!-- Mobile Drawer (Hidden by default) -->
            <div id="mobile-drawer" class="fixed inset-0 z-50 lg:hidden hidden">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" id="mobile-drawer-backdrop"></div>
                <div class="fixed inset-y-0 left-0 w-64 bg-slate-900 text-white p-4 flex flex-col z-10 shadow-2xl">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-teal-600 flex items-center justify-center text-white font-bold">H</div>
                            <span class="font-bold text-white">HealthCare Admin</span>
                        </div>
                        <button type="button" id="close-drawer-btn" class="p-1 rounded text-slate-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <nav class="flex-1 py-4 space-y-1 overflow-y-auto">
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded text-sm {{ request()->routeIs('admin.dashboard*') ? 'bg-teal-600 text-white' : 'text-slate-300' }}">Dashboard</a>
                        <a href="{{ route('admin.patients.index') }}" class="block px-3 py-2 rounded text-sm {{ request()->routeIs('admin.patients*') ? 'bg-teal-600 text-white' : 'text-slate-300' }}">Patients</a>
                        <a href="{{ route('admin.doctors.index') }}" class="block px-3 py-2 rounded text-sm {{ request()->routeIs('admin.doctors*') ? 'bg-teal-600 text-white' : 'text-slate-300' }}">Doctors</a>
                        <a href="{{ route('admin.specialties.index') }}" class="block px-3 py-2 rounded text-sm {{ request()->routeIs('admin.specialties*') ? 'bg-teal-600 text-white' : 'text-slate-300' }}">Specialties</a>
                        <a href="{{ route('admin.appointments.index') }}" class="block px-3 py-2 rounded text-sm {{ request()->routeIs('admin.appointments*') ? 'bg-teal-600 text-white' : 'text-slate-300' }}">Appointments</a>
                        <a href="{{ route('admin.medical-records.index') }}" class="block px-3 py-2 rounded text-sm {{ request()->routeIs('admin.medical-records*') ? 'bg-teal-600 text-white' : 'text-slate-300' }}">Medical Records</a>
                        <a href="{{ route('admin.prescriptions.index') }}" class="block px-3 py-2 rounded text-sm {{ request()->routeIs('admin.prescriptions*') ? 'bg-teal-600 text-white' : 'text-slate-300' }}">Prescriptions</a>
                        <a href="{{ route('admin.payments.index') }}" class="block px-3 py-2 rounded text-sm {{ request()->routeIs('admin.payments*') ? 'bg-teal-600 text-white' : 'text-slate-300' }}">Payments</a>
                        <a href="{{ route('admin.notifications.index') }}" class="block px-3 py-2 rounded text-sm {{ request()->routeIs('admin.notifications*') ? 'bg-teal-600 text-white' : 'text-slate-300' }}">Notifications</a>
                        <a href="{{ route('admin.audit-logs.index') }}" class="block px-3 py-2 rounded text-sm {{ request()->routeIs('admin.audit-logs*') ? 'bg-teal-600 text-white' : 'text-slate-300' }}">Audit Trail</a>
                    </nav>
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">
                <!-- Flash Alerts -->
                @if (session('success'))
                    <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-start gap-3 shadow-xs">
                        <svg class="w-5 h-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm font-medium text-emerald-800">{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 flex items-start gap-3 shadow-xs">
                        <svg class="w-5 h-5 text-rose-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm font-medium text-rose-800">{{ session('error') }}</div>
                    </div>
                @endif

                @if (session('info'))
                    <div class="rounded-xl bg-sky-50 border border-sky-200 p-4 flex items-start gap-3 shadow-xs">
                        <svg class="w-5 h-5 text-sky-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-sm font-medium text-sky-800">{{ session('info') }}</div>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="mt-auto py-4 px-6 border-t border-slate-200 bg-white text-center text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} HealthCare Integrated Medical Platform. All rights reserved. Clinical operational administration.</p>
            </footer>
        </div>
    </div>

    <!-- Interactive script for mobile drawer -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('mobile-menu-btn');
            const drawer = document.getElementById('mobile-drawer');
            const backdrop = document.getElementById('mobile-drawer-backdrop');
            const closeBtn = document.getElementById('close-drawer-btn');

            if (btn && drawer && backdrop && closeBtn) {
                btn.addEventListener('click', () => drawer.classList.remove('hidden'));
                backdrop.addEventListener('click', () => drawer.classList.add('hidden'));
                closeBtn.addEventListener('click', () => drawer.classList.add('hidden'));
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
