<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIATRACK')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">

    @stack('styles')
</head>
<body class="bg-[#fcfbfb] text-gray-800 antialiased min-h-screen flex">

    <!-- Fixed Permanent Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Dynamic Content with Left Margin -->
    <main class="flex-1 flex flex-col min-w-0 pl-64">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>