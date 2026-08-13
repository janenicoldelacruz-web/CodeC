<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Teacher Portal
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-bold">Welcome, {{ Auth::user()->first_name }}!</h3>
            <p class="text-gray-600 mt-2">You are logged into the Admin Dashboard.</p>
        </div>
    </div>
</x-app-layout>