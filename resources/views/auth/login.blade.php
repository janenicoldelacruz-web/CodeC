<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIATRACK - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-200 min-h-screen flex items-center justify-center p-4 font-sans antialiased">

    <!-- Main Container Card -->
    <div class="relative bg-white rounded-3xl shadow-2xl p-8 pt-16 max-w-md w-full mt-10">
        
        <!-- Floating Logo at Top Center -->
        <div class="absolute -top-12 left-1/2 transform -translate-x-1/2 w-24 h-24 bg-white rounded-full p-1.5 shadow-lg border-4 border-white flex items-center justify-center">
            <img src="{{ asset('images/sia-logo.png') }}" alt="School Logo" class="w-full h-full object-contain rounded-full">
        </div>

        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">SIATRACK Portal</h1>
            <p class="text-xs text-gray-400 font-medium mt-0.5 uppercase tracking-wider">
                @if(isset($role)) {{ $role }} Portal @else Login @endif
            </p>
        </div>

        <!-- Role Indicator Badge -->
        @if(isset($role))
            <div class="text-center mb-4">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold capitalize 
                    {{ $role === 'admin' ? 'bg-gray-900 text-white' : '' }}
                    {{ $role === 'teacher' ? 'bg-sky-500 text-white' : '' }}
                    {{ $role === 'student' ? 'bg-emerald-500 text-white' : '' }}">
                    Signing in as {{ $role }}
                </span>
            </div>
        @endif

        <!-- Session Status & Errors -->
        @if (session('status'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-50 text-emerald-600 text-xs text-center">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-50 text-red-600 text-xs text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-gray-600 uppercase tracking-wider mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-medium text-gray-600 uppercase tracking-wider">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs text-blue-600 hover:underline">Forgot?</a>
                    @endif
                </div>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            </div>

            <div class="flex items-center">
                <label class="inline-flex items-center cursor-pointer text-xs text-gray-600">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2">Remember me</span>
                </label>
            </div>

            <button type="submit" 
                class="w-full py-3 px-4 bg-gray-900 hover:bg-gray-800 text-white font-medium text-sm rounded-xl shadow-md transition">
                Sign In
            </button>
        </form>

        <!-- Back to Portal Selection -->
        <div class="mt-6 text-center border-t border-gray-100 pt-4">
            <a href="{{ route('welcome') }}" class="text-xs text-gray-500 hover:text-gray-800 transition">
                &larr; Back to Portal Selection
            </a>
        </div>

    </div>

</body>
</html>