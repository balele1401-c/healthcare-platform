<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Portal Sign In — HealthCare Integrated Medical Platform</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 selection:bg-cyan-500 selection:text-white font-sans antialiased text-slate-100 bg-slate-950">
    <div class="w-full max-w-md space-y-8 bg-slate-900 border border-slate-800 p-8 rounded-3xl shadow-2xl shadow-black/60">
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 items-center justify-center text-white shadow-lg shadow-blue-500/30">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold tracking-tight text-white mt-3">Staff Operations Sign In</h2>
            <p class="text-xs text-slate-400">Clinic Front-Desk & Operational Workspace</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-950/50 border border-rose-800 text-rose-300 text-xs rounded-xl p-3.5 space-y-1">
                @foreach ($errors->all() as $err)
                    <p>&bull; {{ $err }}</p>
                @endforeach
            </div>
        @endif

        @if (session('info'))
            <div class="bg-slate-800 border border-slate-700 text-slate-300 text-xs rounded-xl p-3">
                {{ session('info') }}
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('staff.login.submit') }}" class="space-y-5">
            @csrf

            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold text-slate-300">Staff Email Address</label>
                <div class="relative">
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                           class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-white text-sm placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all"
                           placeholder="staff@healthcare.local">
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-300">Password</label>
                <div class="relative">
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="w-full px-4 py-2.5 bg-slate-800/80 border border-slate-700 rounded-xl text-white text-sm placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all"
                           placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-slate-400 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-800 border-slate-700 text-cyan-600 focus:ring-cyan-500">
                    <span>Keep me signed in</span>
                </label>
            </div>

            <button type="submit"
                    class="w-full py-3 px-4 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold rounded-xl text-sm transition-all shadow-md shadow-cyan-600/30 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                Access Operations Portal &rarr;
            </button>
        </form>

        <div class="pt-4 border-t border-slate-800 text-center text-xs text-slate-500">
            <span>Practitioner or Administrator? </span>
            <div class="mt-1 flex items-center justify-center gap-3">
                <a href="{{ route('doctor.login') }}" class="text-cyan-400 hover:text-cyan-300 font-medium">Doctor Portal</a>
                <span>&bull;</span>
                <a href="{{ route('admin.login') }}" class="text-cyan-400 hover:text-cyan-300 font-medium">Admin Portal</a>
            </div>
        </div>
    </div>
</body>
</html>
