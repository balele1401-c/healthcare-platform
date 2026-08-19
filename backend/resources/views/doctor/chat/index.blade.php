@extends('layouts.doctor')

@section('title', 'Teleconsultation Messaging')
@section('page_title', 'Doctor-Patient Teleconsultation Channels')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden h-[calc(100vh-12rem)] flex flex-col md:flex-row">
    <!-- Left Conversation List -->
    <div class="w-full md:w-80 border-r border-slate-200 flex flex-col bg-slate-50/50 shrink-0">
        <div class="p-4 border-b border-slate-200 bg-white">
            <h3 class="font-bold text-slate-900 text-sm">Consultation Channels</h3>
            <p class="text-[11px] text-slate-500">Authorized direct patient communication</p>
        </div>

        <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
            @forelse ($conversations as $conv)
                <a href="{{ route('doctor.chat.show', $conv->id) }}"
                   class="p-4 flex items-start gap-3 hover:bg-slate-100 transition-colors block {{ isset($activeConversation) && $activeConversation->id === $conv->id ? 'bg-teal-50/70 border-l-4 border-teal-600' : '' }}">
                    <div class="w-9 h-9 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr($conv->patient->user->name ?? 'P', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 truncate">{{ $conv->patient->user->name ?? 'Patient' }}</span>
                            <span class="text-[10px] text-slate-400">{{ $conv->last_message_at ? date('H:i', strtotime($conv->last_message_at)) : '' }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $conv->messages->first()?->message ?? 'No messages yet' }}</p>
                    </div>
                </a>
            @empty
                <div class="p-8 text-center text-slate-400 text-xs">No consultation chat channels active.</div>
            @endforelse
        </div>
    </div>

    <!-- Right Chat Thread -->
    <div class="flex-1 flex flex-col bg-white">
        @if ($activeConversation)
            <!-- Thread Header -->
            <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-white shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr($activeConversation->patient->user->name ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">{{ $activeConversation->patient->user->name ?? 'Patient' }}</h4>
                        <span class="text-[10px] text-slate-400">Patient ID: #PAT-{{ $activeConversation->patient_id }} &bull; Encrypted Teleconsultation</span>
                    </div>
                </div>

                <a href="{{ route('doctor.patients.show', $activeConversation->patient_id) }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                    Open Patient Chart &rarr;
                </a>
            </div>

            <!-- Messages Stream -->
            <div class="flex-1 overflow-y-auto p-5 space-y-4 bg-slate-50/40">
                @forelse ($activeConversation->messages as $msg)
                    @php $isDoctor = $msg->sender_id === Auth::id(); @endphp
                    <div class="flex {{ $isDoctor ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-md rounded-2xl p-3.5 text-xs shadow-xs {{ $isDoctor ? 'bg-teal-600 text-white rounded-br-none' : 'bg-white text-slate-800 border border-slate-200 rounded-bl-none' }}">
                            <p class="leading-relaxed">{{ $msg->message }}</p>
                            <span class="block text-[9px] mt-1 text-right {{ $isDoctor ? 'text-teal-200' : 'text-slate-400' }}">
                                {{ date('H:i', strtotime($msg->created_at)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 text-xs">Start consultation communication with patient below.</div>
                @endforelse
            </div>

            <!-- Reply Form -->
            <div class="p-4 border-t border-slate-200 bg-white shrink-0">
                <form method="POST" action="{{ route('doctor.chat.send', $activeConversation->id) }}" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="message" required placeholder="Type clinical response or instructions..."
                           class="flex-1 text-xs bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <button type="submit" class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl text-xs transition-colors shadow-xs">
                        Send
                    </button>
                </form>
            </div>
        @else
            <div class="flex-1 flex items-center justify-center text-slate-400 text-xs">
                Select a consultation channel to start messaging.
            </div>
        @endif
    </div>
</div>
@endsection
