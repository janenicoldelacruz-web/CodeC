@extends('layouts.app')

@section('title', 'NFC Card Binding - SIATRACK')

@section('content')
<div class="w-full min-h-screen flex flex-col bg-slate-50/70">

    <!-- Top Header Bar -->
    <header class="bg-white border-b-2 border-slate-200 pl-8 lg:pl-12 pr-6 lg:pr-8 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sticky top-0 z-20 shadow-xs w-full">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-sm shadow-xs shrink-0">
                <i class="fa-solid fa-id-card-clip text-amber-300"></i>
            </div>
            <div>
                <h1 class="text-base font-black text-slate-900 tracking-tight">NFC Card Binding</h1>
                <p class="text-[11px] text-slate-500 font-bold">Link physical NFC tags and cards to registered user accounts</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3.5 py-1.5 bg-amber-50 text-amber-900 text-[11px] font-black rounded-xl uppercase border border-amber-300 shadow-2xs flex items-center gap-2">
                <i class="fa-solid fa-wifi text-amber-700 animate-pulse"></i>
                <span>NFC Scanner Ready</span>
            </span>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="pt-6 pb-12 pl-8 lg:pl-12 pr-6 lg:pr-8 w-full space-y-6 flex-1">

        @if(session('success'))
        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="p-3.5 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-bold flex items-center gap-2 shadow-xs">
            <i class="fa-solid fa-triangle-exclamation text-red-600 text-sm"></i>
            <span>Please complete all required fields correctly.</span>
        </div>
        @endif

        <!-- Bind Card Form Card -->
        <div class="bg-white rounded-2xl border-2 border-slate-200 p-6 shadow-xs space-y-4 max-w-2xl">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center text-xs border border-amber-200 shrink-0">
                    <i class="fa-solid fa-link"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">Assign NFC Tag to User</h3>
                    <p class="text-[10px] text-slate-500 font-bold">Select a user and tap or input their card UID</p>
                </div>
            </div>

            <form action="{{ route('admin.nfc.binding.store') ?? '#' }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
                    <!-- Select User -->
                    <div class="sm:col-span-2">
                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-400 mb-1">Select User (Student / Teacher)</label>
                        <select name="user_id" required 
                                class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#8b1818] transition">
                            <option value="" disabled selected>-- Choose User Account --</option>
                            @if(isset($users) && count($users) > 0)
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->last_name }}, {{ $user->first_name }} ({{ ucfirst($user->role->name ?? $user->role ?? 'User') }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- NFC Tag UID Input -->
                    <div class="sm:col-span-2">
                        <label class="block text-[9px] font-black uppercase tracking-wider text-slate-400 mb-1">NFC Card UID / Tag ID</label>
                        <div class="relative">
                            <input type="text" name="tag_id" placeholder="Tap card on hardware reader or type UID manually..." required autocomplete="off"
                                   class="w-full bg-slate-50 border-2 border-slate-200 rounded-xl px-3.5 py-2.5 pl-10 text-xs font-bold font-mono text-slate-900 uppercase focus:outline-none focus:border-[#8b1818] transition">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-wifi text-xs"></i>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-[#8b1818] hover:bg-[#6b1212] text-white text-xs font-black uppercase tracking-wider transition shadow-sm cursor-pointer flex items-center justify-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-down"></i> Save & Bind NFC Card
                    </button>
                </div>
            </form>
        </div>

        <!-- Bound Cards Directory Table -->
        <div class="bg-white rounded-3xl border-2 border-slate-200 p-7 shadow-xs space-y-5">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-9 h-9 rounded-xl bg-[#8b1818] text-white flex items-center justify-center text-sm shadow-sm shrink-0">
                    <i class="fa-solid fa-id-card text-amber-300"></i>
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-900 tracking-tight">Active NFC Card Bindings</h2>
                    <p class="text-[11px] text-slate-500 font-bold">List of all currently paired identification cards in SIATRACK</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b-2 border-slate-100 text-[10px] font-black uppercase tracking-wider text-slate-400">
                            <th class="pb-3 px-4">Card UID</th>
                            <th class="pb-3 px-4">Owner Name</th>
                            <th class="pb-3 px-4">Role / Type</th>
                            <th class="pb-3 px-4">Date Bound</th>
                            <th class="pb-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-bold text-slate-700">
                        @forelse($boundCards ?? [] as $card)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-4 font-mono font-black text-slate-900">
                                <span class="px-2.5 py-1 bg-amber-50 border border-amber-200 text-amber-900 rounded-lg text-[11px]">
                                    {{ $card->tag_id }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-black text-slate-900">
                                {{ $card->user->last_name ?? 'N/A' }}, {{ $card->user->first_name ?? '' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 bg-slate-100 rounded-lg text-slate-800 font-black text-[10px] border border-slate-200 uppercase">
                                    {{ $card->user->role->name ?? $card->user->role ?? 'User' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 font-semibold">
                                {{ $card->created_at ? $card->created_at->format('M d, Y h:i A') : 'N/A' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <form action="#" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to unbind this card?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-[10px] font-black transition cursor-pointer inline-flex items-center gap-1 border border-red-200">
                                        <i class="fa-solid fa-unlink"></i> Unbind
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 font-bold space-y-1">
                                <div class="text-xl text-slate-300"><i class="fa-solid fa-id-card-clip"></i></div>
                                <p class="text-xs">No active NFC cards bound yet. Use the assignment form above.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>
@endsection