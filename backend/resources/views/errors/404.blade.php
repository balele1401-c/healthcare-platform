<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Page Not Found | HealthCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 font-sans text-slate-100 bg-slate-950 selection:bg-cyan-500">
    <div class="text-center space-y-6 max-w-md bg-slate-900 border border-slate-800 p-8 rounded-3xl shadow-2xl">
        <div class="inline-flex w-16 h-16 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 items-center justify-center text-cyan-400 text-2xl font-bold">
            404
        </div>
        <div class="space-y-2">
            <h1 class="text-2xl font-bold text-white tracking-tight">Clinical Resource Not Found</h1>
            <p class="text-xs text-slate-400 leading-relaxed">
                The medical page, appointment URL, or clinical resource you requested could not be located or has been relocated.
            </p>
        </div>
        <div class="pt-2 flex items-center justify-center gap-3">
            <a href="/" class="px-5 py-2.5 bg-cyan-600 hover:bg-cyan-500 text-white font-semibold rounded-xl text-xs transition-colors shadow-md shadow-cyan-600/30">
                Return Home &rarr;
            </a>
            <a href="/doctors" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-xl text-xs border border-slate-700 transition-colors">
                Find Doctors
            </a>
        </div>
    </div>
</body>
</html>
