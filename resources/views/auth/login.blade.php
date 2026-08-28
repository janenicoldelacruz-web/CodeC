@extends('layouts.guest')

@php
    $portalRole = $role ?? 'admin';
    $portalHeader = match($portalRole) {
        'teacher', 'faculty' => 'Faculty Access',
        'student'            => 'Student Access',
        default              => 'Admin Access'
    };
    $targetRegisterRole = match($portalRole) {
        'teacher', 'faculty' => 'teacher',
        default              => 'student'
    };
    $isAdmin = ($portalRole === 'admin');
    $idLabel = ($portalRole === 'student') ? 'Student LRN / School ID' : 'Faculty ID / Employee ID';
    $idPlaceholder = ($portalRole === 'student') ? 'Enter your LRN or ID' : 'Enter your Faculty ID';
@endphp

@section('title', $portalHeader . ' - SIATRACK')

@section('content')
<div class="relative bg-white w-full max-w-6xl rounded-[32px] shadow-2xl border-2 border-slate-300 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[640px] my-6">
    
    <!-- Top Accent Trim -->
    <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#8b1818] via-amber-400 to-[#8b1818] z-30"></div>

    <!-- ================= Left Side: Institutional Photo Banner ================= -->
    <div class="lg:col-span-5 relative bg-[#8b1818] p-10 lg:p-14 flex flex-col justify-between text-white overflow-hidden">
        <div class="absolute inset-0 login-banner-bg mix-blend-multiply opacity-40"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-transparent to-transparent"></div>
        
        <!-- Top Branding -->
        <div class="relative z-10">
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-amber-400 shadow-sm shadow-amber-300"></span>
                <h2 class="text-2xl font-black tracking-tight text-white leading-none">SIATRACK</h2>
            </div>
            <p class="text-xs text-amber-200 font-bold mt-1 tracking-wider uppercase">Southern Isabela Academy</p>
        </div>

        <!-- Center Headline Banner -->
        <div class="relative z-10 my-auto py-8">
            <span class="px-7 py-2.5 bg-amber-400/20 backdrop-blur-md rounded-full text-base font-black uppercase tracking-[0.2em] mb-5 inline-block border-2 border-amber-300/60 text-amber-300 shadow-md shadow-amber-400/10">
                BE A SIAn!
            </span>
            <h3 class="text-3xl lg:text-4xl font-black leading-tight text-white mt-1 drop-shadow-sm">
                Attendance & Evaluation Portal
            </h3>
            <p class="text-sm text-red-100 font-medium mt-3 max-w-md leading-relaxed">
                Automated NFC Attendance Monitoring & Faculty Performance Evaluation System.
            </p>
        </div>

        <!-- Footer -->
        <div class="relative z-10 text-xs text-red-200 font-semibold border-t border-white/20 pt-4">
            © {{ date('Y') }} Southern Isabela Academy • All Rights Reserved.
        </div>
    </div>

    <!-- ================= Right Side: Login Form Card ================= -->
    <div class="lg:col-span-7 p-8 lg:p-14 flex flex-col justify-center bg-white">
        
        <!-- Centered SIA Seal with Gold Ring Accent -->
        <div class="mx-auto w-20 h-20 rounded-full bg-white p-2.5 border-2 border-[#8b1818] ring-4 ring-amber-300/60 shadow-lg flex items-center justify-center mb-4 transition-transform duration-200 hover:scale-105">
            <img src="{{ asset('images/sia-logo.png') }}" alt="SIA Official Seal" class="w-full h-full object-contain">
        </div>

        <!-- Centered Portal Header Title -->
        <div class="text-center mb-6">
            <h2 class="text-2xl lg:text-3xl font-black text-slate-900 tracking-tight">
                {{ $portalHeader }}
            </h2>
            <p class="text-xs text-slate-600 font-bold mt-1">
                @if($isAdmin)
                    Sign in using your admin Gmail and password
                @else
                    Sign in using your ID, Gmail, and password
                @endif
            </p>
        </div>

        <!-- Error Banner -->
        @if($errors->any())
            <div class="mb-5 p-4 bg-red-50 border-2 border-red-300 text-red-800 text-xs font-bold rounded-2xl flex items-start gap-3 shadow-xs">
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="expected_role" value="{{ $portalRole }}">

            @if(!$isAdmin)
                <!-- Input 1: ID Number / LRN (Hidden for Admin) -->
                <div>
                    <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wide mb-1.5">
                        {{ $idLabel }} <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="id_number" value="{{ old('id_number') }}" required 
                           placeholder="{{ $idPlaceholder }}"
                           class="w-full py-3 px-4 text-sm font-bold text-slate-900 border-2 border-slate-300 rounded-2xl bg-white focus:outline-none focus:border-[#8b1818]">
                </div>
            @endif

            <!-- Input 2: Gmail / Email Address -->
            <div>
                <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wide mb-1.5">
                    Gmail Address <span class="text-red-600">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                       placeholder="{{ $isAdmin ? 'admin@siatrack.edu.ph' : 'Enter your registered Gmail' }}"
                       class="w-full py-3 px-4 text-sm font-bold text-slate-900 border-2 border-slate-300 rounded-2xl bg-white focus:outline-none focus:border-[#8b1818]">
            </div>

            <!-- Input 3: Password -->
            <div>
                <label class="block text-xs font-extrabold text-slate-900 uppercase tracking-wide mb-1.5">
                    Password <span class="text-red-600">*</span>
                </label>
                <div class="relative flex items-center">
                    <input type="password" id="login_password" name="password" required placeholder="••••••••"
                           class="w-full py-3 px-4 pr-16 text-sm font-bold text-slate-900 border-2 border-slate-300 rounded-2xl bg-white focus:outline-none focus:border-[#8b1818]">
                    <button type="button" onclick="togglePass('login_password', this)" class="absolute right-4 text-xs font-bold text-slate-500 hover:text-slate-800 focus:outline-none cursor-pointer">
                        Show
                    </button>
                </div>
            </div>

            <!-- Remember Me & Portal Switch -->
            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-700 select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#8b1818] focus:ring-[#8b1818] border-slate-300">
                    <span>Remember me</span>
                </label>
                <a href="{{ route('welcome') }}" class="font-extrabold text-[#8b1818] hover:text-amber-600 transition">
                    Back to Main Portal
                </a>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full py-4 rounded-2xl bg-[#8b1818] hover:bg-[#751414] text-white font-black text-sm tracking-wider uppercase shadow-lg shadow-red-950/20 active:scale-[0.99] mt-3 border-b-4 border-[#5e0f0f] cursor-pointer">
                Sign In
            </button>

            <!-- Role-Aware Register Link (Hidden for Admins) -->
            @if(!$isAdmin)
                <div class="text-center pt-2 text-xs text-slate-600 font-semibold">
                    <span>Don't have an account?</span>
                    <a href="{{ route('register', ['role' => $targetRegisterRole]) }}" class="font-extrabold text-[#8b1818] hover:text-amber-600 hover:underline ml-1 transition-colors">
                        Register here
                    </a>
                </div>
            @endif
        </form>

    </div>

</div>

@push('scripts')
<script>
    function togglePass(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        
        if (input.type === 'password') {
            input.type = 'text';
            btn.innerText = 'Hide';
        } else {
            input.type = 'password';
            btn.innerText = 'Show';
        }
    }
</script>
@endpush
@endsection