<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta Tags -->
    <title>@yield('meta_title', 'HealthCare — Personal Health Monitoring & Doctor Consultation Platform')</title>
    <meta name="description" content="@yield('meta_description', 'Connect with certified healthcare practitioners, schedule in-person and teleconsultation appointments, manage electronic medical records, and track personal vital signs with HealthCare.')">
    <meta name="keywords" content="healthcare, telemedicine, doctor consultation, medical records, digital prescriptions, health tracker, appointment booking">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('meta_title', 'HealthCare — Integrated Medical Platform')">
    <meta property="og:description" content="@yield('meta_description', 'Your health, connected. Schedule doctor appointments, manage digital prescriptions, and monitor your vitals securely.')">
    <meta property="og:image" content="{{ asset('images/og-healthcare.png') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('meta_title', 'HealthCare — Integrated Medical Platform')">
    <meta name="twitter:description" content="@yield('meta_description', 'Your health, connected. Schedule doctor appointments, manage digital prescriptions, and monitor your vitals securely.')">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-700 bg-slate-50 flex flex-col min-h-full selection:bg-teal-500 selection:text-white">
    <!-- Top Global Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-100 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('public.home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-cyan-600 to-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-lg text-slate-900 tracking-tight block leading-tight">HealthCare</span>
                    <span class="text-[10px] font-semibold text-cyan-600 tracking-wider uppercase block">Medical Platform</span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-slate-600">
                <a href="{{ route('public.home') }}" class="hover:text-cyan-600 transition-colors {{ request()->routeIs('public.home') ? 'text-cyan-600 font-semibold' : '' }}">Home</a>
                <a href="{{ route('public.about') }}" class="hover:text-cyan-600 transition-colors {{ request()->routeIs('public.about') ? 'text-cyan-600 font-semibold' : '' }}">About</a>
                <a href="{{ route('public.services') }}" class="hover:text-cyan-600 transition-colors {{ request()->routeIs('public.services') ? 'text-cyan-600 font-semibold' : '' }}">Services</a>
                <a href="{{ route('public.doctors') }}" class="hover:text-cyan-600 transition-colors {{ request()->routeIs('public.doctors') ? 'text-cyan-600 font-semibold' : '' }}">Doctors</a>
                <a href="{{ route('public.how-it-works') }}" class="hover:text-cyan-600 transition-colors {{ request()->routeIs('public.how-it-works') ? 'text-cyan-600 font-semibold' : '' }}">How It Works</a>
                <a href="{{ route('public.faq') }}" class="hover:text-cyan-600 transition-colors {{ request()->routeIs('public.faq') ? 'text-cyan-600 font-semibold' : '' }}">FAQ</a>
                <a href="{{ route('public.contact') }}" class="hover:text-cyan-600 transition-colors {{ request()->routeIs('public.contact') ? 'text-cyan-600 font-semibold' : '' }}">Contact</a>
            </nav>

            <!-- Portals CTA Dropdown & Action Buttons -->
            <div class="hidden md:flex items-center gap-3">
                <div class="relative group" id="portals-dropdown">
                    <button type="button" class="px-3.5 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors flex items-center gap-1.5">
                        <span>Portals & Login</span>
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 mt-1 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 hidden group-hover:block transition-all z-50">
                        <a href="{{ route('doctor.login') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-600 rounded-xl">
                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                            Doctor Clinical Portal
                        </a>
                        <a href="{{ route('staff.login') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-600 rounded-xl">
                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                            Staff Operations Portal
                        </a>
                        <a href="{{ route('admin.login') }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-cyan-600 rounded-xl">
                            <span class="w-2 h-2 rounded-full bg-slate-700"></span>
                            Administration Portal
                        </a>
                    </div>
                </div>

                <a href="{{ route('public.doctors') }}" class="px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white font-semibold rounded-xl text-xs shadow-md shadow-cyan-600/20 transition-all">
                    Find a Doctor &rarr;
                </a>
            </div>

            <!-- Mobile Menu Trigger -->
            <button type="button" id="mobile-toggle" class="md:hidden p-2.5 rounded-xl text-slate-600 hover:bg-slate-100">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Mobile Drawer -->
        <div id="mobile-menu" class="md:hidden hidden border-b border-slate-200 bg-white px-6 py-5 space-y-4">
            <nav class="flex flex-col space-y-3 text-sm font-medium text-slate-700">
                <a href="{{ route('public.home') }}" class="py-1">Home</a>
                <a href="{{ route('public.about') }}" class="py-1">About</a>
                <a href="{{ route('public.services') }}" class="py-1">Services</a>
                <a href="{{ route('public.doctors') }}" class="py-1">Doctors Directory</a>
                <a href="{{ route('public.how-it-works') }}" class="py-1">How It Works</a>
                <a href="{{ route('public.faq') }}" class="py-1">FAQ</a>
                <a href="{{ route('public.contact') }}" class="py-1">Contact</a>
            </nav>
            <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                <a href="{{ route('doctor.login') }}" class="text-xs text-slate-600 py-1.5">Doctor Clinical Portal</a>
                <a href="{{ route('staff.login') }}" class="text-xs text-slate-600 py-1.5">Staff Operations Portal</a>
                <a href="{{ route('admin.login') }}" class="text-xs text-slate-600 py-1.5">Admin Management Portal</a>
                <a href="{{ route('public.doctors') }}" class="w-full py-2.5 text-center bg-cyan-600 text-white font-semibold rounded-xl text-xs mt-2">
                    Find a Doctor
                </a>
            </div>
        </div>
    </header>

    <!-- Main Body Slot -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Comprehensive Public Footer -->
    <footer class="bg-slate-950 text-slate-400 text-xs border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-10">
                <!-- Brand Info (Col 1-2) -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-cyan-500 flex items-center justify-center text-white font-bold">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <span class="text-base font-bold text-white">HealthCare Platform</span>
                    </div>
                    <p class="text-slate-400 text-xs leading-relaxed max-w-sm">
                        An integrated, HIPAA-aware clinical coordination and telemedicine platform connecting patients with certified medical specialists, electronic medical records, and continuous vital signs monitoring.
                    </p>
                    <div class="text-[11px] text-slate-500 pt-2">
                        &copy; {{ date('Y') }} HealthCare Integrated Medical Platform. All rights reserved.
                    </div>
                </div>

                <!-- Navigation Links (Col 3) -->
                <div class="space-y-3">
                    <h4 class="font-bold text-white uppercase text-[11px] tracking-wider">Navigation</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('public.home') }}" class="hover:text-cyan-400 transition-colors">Home</a></li>
                        <li><a href="{{ route('public.about') }}" class="hover:text-cyan-400 transition-colors">About Us</a></li>
                        <li><a href="{{ route('public.services') }}" class="hover:text-cyan-400 transition-colors">Clinical Services</a></li>
                        <li><a href="{{ route('public.doctors') }}" class="hover:text-cyan-400 transition-colors">Find a Doctor</a></li>
                        <li><a href="{{ route('public.how-it-works') }}" class="hover:text-cyan-400 transition-colors">How It Works</a></li>
                    </ul>
                </div>

                <!-- Portals & Support (Col 4) -->
                <div class="space-y-3">
                    <h4 class="font-bold text-white uppercase text-[11px] tracking-wider">Access Portals</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('doctor.login') }}" class="hover:text-cyan-400 transition-colors">Doctor Portal</a></li>
                        <li><a href="{{ route('staff.login') }}" class="hover:text-cyan-400 transition-colors">Staff Operations</a></li>
                        <li><a href="{{ route('admin.login') }}" class="hover:text-cyan-400 transition-colors">Admin Dashboard</a></li>
                        <li><a href="{{ route('public.faq') }}" class="hover:text-cyan-400 transition-colors">Frequently Asked Questions</a></li>
                        <li><a href="{{ route('public.contact') }}" class="hover:text-cyan-400 transition-colors">Contact Support</a></li>
                    </ul>
                </div>

                <!-- Legal & Compliance (Col 5) -->
                <div class="space-y-3">
                    <h4 class="font-bold text-white uppercase text-[11px] tracking-wider">Legal & Privacy</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('public.privacy') }}" class="hover:text-cyan-400 transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('public.terms') }}" class="hover:text-cyan-400 transition-colors">Terms of Service</a></li>
                        <li><a href="{{ url('/sitemap.xml') }}" class="hover:text-cyan-400 transition-colors">XML Sitemap</a></li>
                        <li><a href="{{ url('/robots.txt') }}" class="hover:text-cyan-400 transition-colors">Robots.txt</a></li>
                    </ul>
                </div>
            </div>

            <!-- Medical Disclaimer Banner (Required for Compliance & Mayar Verification) -->
            <div class="mt-12 pt-8 border-t border-slate-900 text-[11px] text-slate-500 leading-relaxed space-y-2">
                <p>
                    <strong class="text-slate-400">Important Medical Disclaimer:</strong> HealthCare provides clinical coordination, appointment scheduling, and electronic health information management. It is not an emergency response service. If you are experiencing a life-threatening medical emergency, please call your local emergency services immediately.
                </p>
                <p>
                    Payment and billing integration is currently operating in sandbox/readiness mode pending final financial institution verification.
                </p>
            </div>
        </div>
    </footer>

    <!-- Mobile Drawer Toggle Script -->
    <script>
        const toggleBtn = document.getElementById('mobile-toggle');
        const menu = document.getElementById('mobile-menu');
        if (toggleBtn && menu) {
            toggleBtn.addEventListener('click', () => {
                menu.classList.toggle('hidden');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
