<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIATRACK - Portal Selection</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-200 min-h-screen flex items-center justify-center p-4 font-sans antialiased">

    <!-- Main Card Container -->
    <div class="relative bg-white rounded-3xl shadow-2xl p-8 pt-16 max-w-md w-full text-center mt-10">
        
        <!-- Floating Logo at Top Center -->
        <div class="absolute -top-12 left-1/2 transform -translate-x-1/2 w-24 h-24 bg-white rounded-full p-1.5 shadow-lg border-4 border-white flex items-center justify-center">
            <img src="{{ asset('images/sia-logo.png') }}" alt="Southern Isabela Academy Logo" class="w-full h-full object-contain rounded-full">
        </div>

        <!-- Portal Header Titles -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">SIATRACK Portal</h1>
            <p class="text-xs text-gray-400 font-medium mt-0.5">Southern Isabela Academy</p>
        </div>

        <!-- Section Divider: Admin & Faculty -->
        <div class="relative flex py-3 items-center">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="flex-shrink mx-3 text-gray-400 text-xs font-medium">Admin & Faculty Access</span>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <!-- Action Buttons (Faculty & Admin) -->
        <div class="space-y-3.5 my-4">
            
            <!-- Faculty Login Button -->
            <a href="{{ route('login.portal', 'teacher') }}" 
               class="w-full py-3 px-4 bg-gradient-to-r from-sky-400 to-blue-500 hover:from-sky-500 hover:to-blue-600 text-white font-medium text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Faculty Login
            </a>

            <!-- Admin Access Button -->
            <a href="{{ route('login.portal', 'admin') }}" 
               class="w-full py-3 px-4 bg-gray-900 hover:bg-gray-800 text-white font-medium text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.654 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Admin Access
            </a>

        </div>

        <!-- Section Divider: Student -->
        <div class="relative flex py-3 items-center">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="flex-shrink mx-3 text-gray-400 text-xs font-medium">Student Access</span>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>

        <!-- Student Portal Button -->
        <div class="mt-4">
            <a href="{{ route('login.portal', 'student') }}" 
               class="w-full py-3 px-4 bg-gradient-to-r from-emerald-400 to-green-600 hover:from-emerald-500 hover:to-green-700 text-white font-medium text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Student Portal
            </a>
        </div>

    </div>

</body>
</html>