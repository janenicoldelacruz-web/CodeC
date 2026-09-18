@extends('layouts.app')

@section('title', 'Messages | SIATRACK')

@section('content')
<div class="p-6 md:p-8 max-w-7xl mx-auto w-full">
    
    <!-- Page Header (SIATRACK Theme) -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white p-1 border-2 border-amber-300 shadow-sm flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-envelope text-[#590d0d] text-lg"></i>
                </div>
                Messages & Inbox
            </h1>
            <p class="text-sm font-semibold text-gray-500 mt-2 ml-1">
                View your conversations, system alerts, and SMS logs.
            </p>
        </div>
        
        <!-- Compose Button -->
        <div>
            <button class="bg-[#590d0d] hover:bg-red-950 text-amber-300 px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm transition flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Compose Message
            </button>
        </div>
    </div>

    <!-- Messages Inbox Table -->
    <div>
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-inbox text-amber-500"></i>
            Recent Messages
        </h2>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-[#590d0d] text-amber-300 font-bold uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-4 w-12 text-center"><i class="fa-solid fa-envelope-open-text"></i></th>
                            <th class="p-4">Sender</th>
                            <th class="p-4">Message Snippet</th>
                            <th class="p-4">Date & Time</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                        @forelse($messages as $msg)
                            <tr class="hover:bg-amber-50/50 transition duration-150 {{ (isset($msg->is_read) && !$msg->is_read) ? 'bg-amber-50/30' : '' }}">
                                <td class="p-4 text-center">
                                    <i class="fa-solid {{ (isset($msg->is_read) && !$msg->is_read) ? 'fa-envelope text-amber-500' : 'fa-envelope-open text-gray-300' }}"></i>
                                </td>
                                <td class="p-4 font-black text-[#590d0d]">
                                    {{ $msg->last_name ?? 'System' }}, {{ $msg->first_name ?? 'Admin' }}
                                </td>
                                <td class="p-4">
                                    <!-- Puputulin ang text kung masyadong mahaba -->
                                    <span class="truncate block max-w-xs text-[13px] {{ (isset($msg->is_read) && !$msg->is_read) ? 'font-bold text-gray-800' : 'text-gray-500' }}">
                                        {{ $msg->content ?? 'No content available.' }}
                                    </span>
                                </td>
                                <td class="p-4 text-[12px] font-semibold text-gray-500">
                                    {{ $msg->created_at ? \Carbon\Carbon::parse($msg->created_at)->format('M d, Y • h:i A') : 'N/A' }}
                                </td>
                                <td class="p-4 text-center">
                                    <button class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 border border-teal-200 hover:bg-teal-500 hover:text-white transition" title="Read Message">
                                        <i class="fa-solid fa-reply"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <!-- Empty State Kung Walang Messages o Wala pang Table -->
                            <tr>
                                <td colspan="5" class="p-10 text-center text-gray-400 font-medium text-sm">
                                    <div class="mx-auto w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3 border border-gray-100">
                                        <i class="fa-regular fa-comments text-3xl opacity-50"></i>
                                    </div>
                                    No messages found in your inbox.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3.5 bg-gray-50 border-t border-gray-100 text-xs font-semibold text-gray-500 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved text-amber-500"></i> 
                All communications are secured and logged by the system.
            </div>
        </div>
    </div>

</div>
@endsection