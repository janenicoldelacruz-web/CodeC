@extends('layouts.guest')

@section('title', 'SIATRACK Login')

@section('content')

<div
    class="fixed inset-0 z-50 h-screen w-screen overflow-hidden bg-[#f4f4f5] font-['Plus_Jakarta_Sans',sans-serif]"
    style="height: 100dvh; min-height: 100dvh; overflow: hidden;"
>

    <!-- Top Accent -->
    <div class="absolute top-0 left-0 right-0 z-[100] h-1 bg-gradient-to-r from-[#5c0d0d] via-amber-400 to-[#8b1818]"></div>


    <div class="grid h-full w-full grid-cols-1 lg:grid-cols-[38%_62%]">


<!-- ========================================================= -->
<!-- LEFT LANDSCAPE RED CARD -->
<!-- ========================================================= -->

<section class="relative hidden h-full min-h-0 items-center justify-center p-5 xl:p-7 lg:flex">

    <!-- LANDSCAPE CARD -->
    <div
        class="relative h-[82%] w-full overflow-hidden rounded-[2rem] bg-[#701010] text-white shadow-2xl shadow-red-950/30"
    >

        <!-- Background Image -->
        <div
            class="login-banner-bg absolute inset-0 scale-105 bg-cover bg-center"
        ></div>

        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-[#5c0d0d]/75"></div>

        <!-- Gradient -->
        <div
            class="absolute inset-0 bg-gradient-to-br from-[#450606]/95 via-[#701010]/85 to-[#a32323]/75"
        ></div>


        <!-- ===================================================== -->
        <!-- DECORATIVE GLOW -->
        <!-- ===================================================== -->

        <div
            class="absolute -right-32 -top-32 h-[420px] w-[420px] rounded-full bg-amber-300/10 blur-3xl"
        ></div>

        <div
            class="absolute -bottom-32 -left-32 h-[350px] w-[350px] rounded-full bg-red-300/10 blur-3xl"
        ></div>


        <!-- Decorative Circles -->
        <div
            class="absolute -right-32 -top-32 h-[420px] w-[420px] rounded-full border border-white/10"
        ></div>

        <div
            class="absolute -right-16 -top-16 h-[280px] w-[280px] rounded-full border border-white/10"
        ></div>

        <div
            class="absolute -bottom-40 -left-40 h-[420px] w-[420px] rounded-full border border-white/10"
        ></div>


        <!-- Dot Pattern -->
        <div
            class="absolute inset-0 opacity-[0.07]"
            style="
                background-image: radial-gradient(circle, white 1px, transparent 1px);
                background-size: 22px 22px;
            "
        ></div>



        <!-- ===================================================== -->
        <!-- CARD CONTENT -->
        <!-- ===================================================== -->

        <div
            class="relative z-10 flex h-full w-full flex-col justify-between px-7 py-7 xl:px-10 xl:py-9"
        >


            <!-- ================================================= -->
            <!-- TOP BRANDING -->
            <!-- ================================================= -->

            <div class="flex items-center justify-between">

                <!-- Brand -->
                <div class="flex items-center gap-3">

                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white shadow-xl shadow-black/20"
                    >

                        <img
                            src="{{ asset('images/sia-logo.png') }}"
                            alt="SIA Logo"
                            class="h-10 w-10 object-contain"
                        >

                    </div>


                    <div>

                        <p class="text-base font-black tracking-wide">
                            SIATRACK
                        </p>

                        <p class="text-[10px] font-medium text-white/60">
                            Southern Isabela Academy
                        </p>

                    </div>

                </div>


                <!-- System Online -->
                <div
                    class="flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-2 backdrop-blur-md"
                >

                    <span class="relative flex h-2 w-2">

                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-60"
                        ></span>

                        <span
                            class="relative inline-flex h-2 w-2 rounded-full bg-green-400"
                        ></span>

                    </span>


                    <span
                        class="text-[9px] font-bold uppercase tracking-[0.16em] text-white/80"
                    >
                        System Online
                    </span>

                </div>

            </div>



            <!-- ================================================= -->
            <!-- LANDSCAPE MAIN CONTENT -->
            <!-- ================================================= -->

            <div class="grid items-center gap-8 xl:grid-cols-[1.1fr_0.9fr] xl:gap-10">


                <!-- LEFT SIDE -->
                <div>

                    <!-- Label -->
                    <div
                        class="mb-4 inline-flex items-center rounded-full border border-amber-300/20 bg-amber-300/10 px-3.5 py-1.5"
                    >

                        <span
                            class="text-[10px] font-extrabold uppercase tracking-[0.18em] text-amber-200"
                        >
                            Attendance & Evaluation Portal
                        </span>

                    </div>


                    <!-- Heading -->
                    <h2
                        class="text-4xl font-black leading-[0.95] tracking-tight xl:text-5xl 2xl:text-6xl"
                    >

                        BE A

                        <span class="text-amber-300">
                            SIAn!
                        </span>

                    </h2>


                    <!-- Description -->
                    <p
                        class="mt-4 max-w-lg text-sm leading-relaxed text-white/65 xl:text-base"
                    >

                        Access a simple and secure platform for
                        attendance tracking, faculty evaluation,
                        and academic management.

                    </p>


                    <!-- Small Accent -->
                    <div class="mt-5 flex items-center gap-2">

                        <div class="h-1 w-10 rounded-full bg-amber-300"></div>

                        <div class="h-1 w-3 rounded-full bg-white/30"></div>

                        <div class="h-1 w-2 rounded-full bg-white/20"></div>

                    </div>

                </div>



                <!-- ================================================= -->
                <!-- RIGHT FEATURE AREA -->
                <!-- ================================================= -->

                <div class="space-y-3">


                    <!-- NFC -->
                    <div
                        class="group rounded-2xl border border-white/10 bg-white/[0.08] p-4 backdrop-blur-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-white/[0.13]"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-300/10 ring-1 ring-amber-300/10"
                            >

                                <svg
                                    class="h-5 w-5 text-amber-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.8"
                                        d="M12 18h.01M8.5 8.5a5 5 0 017 0M5.5 5.5a9.24 9.24 0 0113 0M2.5 2.5a13.48 13.48 0 0119 0"
                                    />

                                </svg>

                            </div>


                            <div>

                                <p class="text-sm font-extrabold">
                                    NFC Attendance
                                </p>

                                <p class="mt-1 text-[10px] leading-relaxed text-white/50 xl:text-xs">
                                    Fast and convenient attendance tracking.
                                </p>

                            </div>

                        </div>

                    </div>



                    <!-- Faculty Evaluation -->
                    <div
                        class="group rounded-2xl border border-white/10 bg-white/[0.08] p-4 backdrop-blur-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-white/[0.13]"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-300/10 ring-1 ring-amber-300/10"
                            >

                                <svg
                                    class="h-5 w-5 text-amber-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.8"
                                        d="M9 12l2 2 4-4M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"
                                    />

                                </svg>

                            </div>


                            <div>

                                <p class="text-sm font-extrabold">
                                    Faculty Evaluation
                                </p>

                                <p class="mt-1 text-[10px] leading-relaxed text-white/50 xl:text-xs">
                                    Simple and secure evaluation management.
                                </p>

                            </div>

                        </div>

                    </div>



                    <!-- Academic Management -->
                    <div
                        class="group rounded-2xl border border-white/10 bg-white/[0.08] p-4 backdrop-blur-md transition-all duration-200 hover:-translate-y-0.5 hover:bg-white/[0.13]"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-300/10 ring-1 ring-amber-300/10"
                            >

                                <svg
                                    class="h-5 w-5 text-amber-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.8"
                                        d="M4 5a2 2 0 012-2h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V5zM8 7h8M8 11h8M8 15h5"
                                    />

                                </svg>

                            </div>


                            <div>

                                <p class="text-sm font-extrabold">
                                    Academic Management
                                </p>

                                <p class="mt-1 text-[10px] leading-relaxed text-white/50 xl:text-xs">
                                    Organized and accessible academic information.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>



            <!-- ================================================= -->
            <!-- FOOTER -->
            <!-- ================================================= -->

            <div
                class="flex items-center justify-between border-t border-white/10 pt-4"
            >

                <div class="flex items-center gap-2">

                    <div class="h-1.5 w-1.5 rounded-full bg-amber-300"></div>

                    <span class="text-[10px] font-medium text-white/40">
                        Secure Academic Platform
                    </span>

                </div>


                <span class="text-[10px] font-medium text-white/35">
                    © {{ date('Y') }} SIATRACK
                </span>

            </div>

        </div>

    </div>

</section>

        <!-- ========================================================= -->
        <!-- RIGHT LOGIN SECTION -->
        <!-- ========================================================= -->

        <section
            class="relative flex h-full min-h-0 items-center justify-center overflow-hidden bg-[#f4f4f5] px-5 py-6 sm:px-8 lg:px-12 xl:px-20"
        >


            <!-- Decorative Background -->
            <div class="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full bg-red-100/50 blur-3xl"></div>

            <div class="pointer-events-none absolute -bottom-32 -left-32 h-96 w-96 rounded-full bg-amber-100/40 blur-3xl"></div>


            <!-- Grid Background -->
            <div
                class="pointer-events-none absolute inset-0 opacity-[0.025]"
                style="
                    background-image:
                        linear-gradient(#701010 1px, transparent 1px),
                        linear-gradient(90deg, #701010 1px, transparent 1px);
                    background-size: 40px 40px;
                "
            ></div>


            <!-- LOGIN CONTAINER -->
            <div class="relative z-10 flex w-full max-w-[620px] flex-col justify-center">


                <!-- Mobile Branding -->
                <div class="mb-5 flex items-center justify-center gap-3 lg:hidden">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white shadow-md">

                        <img
                            src="{{ asset('images/sia-logo.png') }}"
                            alt="SIA Logo"
                            class="h-9 w-9 object-contain"
                        >

                    </div>


                    <div>

                        <p class="text-base font-extrabold tracking-wide text-[#701010]">
                            SIATRACK
                        </p>

                        <p class="text-[10px] font-medium text-gray-500">
                            Southern Isabela Academy
                        </p>

                    </div>

                </div>



                <!-- Logo -->
                <div class="flex justify-center">

                    <div class="flex h-24 w-24 items-center justify-center rounded-3xl bg-white p-2.5 shadow-2xl shadow-gray-300/60 sm:h-28 sm:w-28">

                        <img
                            src="{{ asset('images/sia-logo.png') }}"
                            alt="Southern Isabela Academy"
                            class="h-full w-full object-contain"
                        >

                    </div>

                </div>



                <!-- Header -->
                <div class="mt-5 text-center">

                    <div class="mb-3 inline-flex items-center rounded-full bg-[#701010]/10 px-4 py-1.5">

                        <span class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-[#701010]">
                            Secure Portal
                        </span>

                    </div>


                    <h1 class="text-4xl font-black tracking-tight text-gray-900 sm:text-5xl">
                        Welcome Back
                    </h1>


                    <p class="mt-2 text-base text-gray-500 sm:text-lg">
                        Sign in to access your SIATRACK account
                    </p>

                </div>



                <!-- Errors -->
                @if ($errors->any())

                    <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 p-4">

                        <div class="flex gap-3">

                            <svg
                                class="mt-0.5 h-5 w-5 shrink-0 text-red-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4m0 4h.01M10.29 3.86l-7.82 13a2 2 0 001.71 2.14h15.64a2 2 0 001.71-2.14l-7.82-13a2 2 0 00-3.42 0z"
                                />

                            </svg>


                            <div>

                                <p class="text-sm font-bold text-red-700">
                                    Login failed
                                </p>


                                <ul class="mt-1 space-y-0.5 text-xs text-red-600">

                                    @foreach ($errors->all() as $error)

                                        <li>
                                            {{ $error }}
                                        </li>

                                    @endforeach

                                </ul>

                            </div>

                        </div>

                    </div>

                @endif



                <!-- Login Card -->
                <div class="mt-6 rounded-[2rem] border border-gray-200 bg-white p-7 shadow-2xl shadow-gray-300/50 sm:p-9">


                    <form
                        method="POST"
                        action="{{ route('login.submit') }}"
                        class="space-y-6"
                    >

                        @csrf


                        <!-- Email -->
                        <div>

                            <label
                                for="email"
                                class="mb-2.5 block text-base font-bold text-gray-700"
                            >
                                Email Address
                            </label>


                            <div class="relative">

                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-5">

                                    <svg
                                        class="h-5 w-5 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                        />

                                    </svg>

                                </div>


                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    autocomplete="email"
                                    placeholder="Enter your email address"
                                    class="h-14 w-full rounded-2xl border border-gray-200 bg-gray-50 pl-14 pr-5 text-base font-medium text-gray-900 outline-none transition focus:border-[#701010] focus:bg-white focus:ring-4 focus:ring-[#701010]/10"
                                >

                            </div>

                        </div>



                        <!-- Password -->
                        <div>

                            <label
                                for="login_password"
                                class="mb-2.5 block text-base font-bold text-gray-700"
                            >
                                Password
                            </label>


                            <div class="relative">

                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-5">

                                    <svg
                                        class="h-5 w-5 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M7 10V7a5 5 0 0110 0v3m-9 0h8a2 2 0 012 2v7a2 2 0 01-2 2H8a2 2 0 01-2-2v-7a2 2 0 012-2z"
                                        />

                                    </svg>

                                </div>


                                <input
                                    type="password"
                                    id="login_password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Enter your password"
                                    class="h-14 w-full rounded-2xl border border-gray-200 bg-gray-50 pl-14 pr-14 text-base font-medium text-gray-900 outline-none transition focus:border-[#701010] focus:bg-white focus:ring-4 focus:ring-[#701010]/10"
                                >


                                <!-- Password Toggle -->
                                <button
                                    type="button"
                                    onclick="togglePass('login_password', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-5 text-gray-400 transition hover:text-[#701010]"
                                    aria-label="Show password"
                                >

                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"
                                        />

                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="2.5"
                                            stroke-width="1.8"
                                        />

                                    </svg>

                                </button>

                            </div>

                        </div>



                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">

                            <label class="flex cursor-pointer items-center gap-2">

                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="h-4 w-4 rounded border-gray-300 text-[#701010] focus:ring-[#701010]"
                                >


                                <span class="text-sm font-medium text-gray-600">
                                    Remember me
                                </span>

                            </label>


                            <a
                                href="{{ route('welcome') }}"
                                class="text-sm font-bold text-[#701010] transition hover:text-[#4d0808]"
                            >
                                Back to Portal
                            </a>

                        </div>



                        <!-- Sign In Button -->
                        <button
                            type="submit"
                            class="group relative flex h-[68px] w-full items-center justify-center overflow-hidden rounded-2xl bg-[#701010] px-6 text-lg font-extrabold text-white shadow-xl shadow-red-900/25 transition-all duration-200 hover:bg-[#5c0d0d] hover:shadow-2xl hover:shadow-red-900/30 active:scale-[0.98]"
                        >

                            <span class="relative z-10 flex items-center gap-3">

                                Sign In

                                <svg
                                    class="h-6 w-6 transition-transform duration-200 group-hover:translate-x-1"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6"
                                    />

                                </svg>

                            </span>

                        </button>

                    </form>

                </div>



                <!-- Security Footer -->
                <div class="mt-5 flex items-center justify-center gap-2 text-xs font-medium text-gray-400">

                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z"
                        />

                    </svg>


                    <span>
                        Your account information is securely protected
                    </span>

                </div>

            </div>

        </section>

    </div>

</div>



<!-- ========================================================= -->
<!-- STYLES -->
<!-- ========================================================= -->

<style>

    html,
    body {
        margin: 0;
        padding: 0;
    }


    .login-banner-bg {
        background-size: cover;
        background-position: center;
    }


    input:-webkit-autofill,
    input:-webkit-autofill:hover,
    input:-webkit-autofill:focus {

        -webkit-text-fill-color: #111827;

        -webkit-box-shadow: 0 0 0px 1000px #f9fafb inset;

        transition: background-color 5000s ease-in-out 0s;

    }

</style>



<!-- ========================================================= -->
<!-- PASSWORD SCRIPT -->
<!-- ========================================================= -->

<script>

    function togglePass(inputId, button) {

        const input = document.getElementById(inputId);

        if (!input) return;


        if (input.type === 'password') {

            input.type = 'text';

            button.setAttribute('aria-label', 'Hide password');


            button.innerHTML = `
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8M9.9 5.2A10.8 10.8 0 0112 5c6 0 9.5 7 9.5 7a17.7 17.7 0 01-3.2 4.1M6.1 6.1C3.8 8 2.5 12 2.5 12s3.5 6 9.5 6c1.2 0 2.3-.2 3.3-.6"
                    />

                </svg>
            `;

        } else {

            input.type = 'password';

            button.setAttribute('aria-label', 'Show password');


            button.innerHTML = `
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"
                    />

                    <circle
                        cx="12"
                        cy="12"
                        r="2.5"
                        stroke-width="1.8"
                    />

                </svg>
            `;

        }

    }

</script>

@endsection