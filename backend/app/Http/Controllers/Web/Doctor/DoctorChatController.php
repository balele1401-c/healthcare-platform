<?php

namespace App\Http\Controllers\Web\Doctor;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DoctorChatController extends Controller
{
    /**
     * Display list of patient consultation channels.
     */
    public function index(): View
    {
        $doctor = Auth::user()->doctor;

        $conversations = ChatConversation::where('doctor_id', $doctor->id)
            ->with(['patient.user', 'messages' => fn ($q) => $q->latest()->take(1)])
            ->latest('last_message_at')
            ->get();

        $activeConversation = $conversations->first();

        if ($activeConversation) {
            $activeConversation->load(['patient.user', 'messages' => fn ($q) => $q->oldest()]);
        }

        return view('doctor.chat.index', compact('conversations', 'activeConversation'));
    }

    /**
     * Display a specific consultation conversation thread.
     */
    public function show(ChatConversation $conversation): View
    {
        $doctor = Auth::user()->doctor;

        if ($conversation->doctor_id !== $doctor->id) {
            abort(403, 'Access denied. You may only view consultation channels assigned to you.');
        }

        $conversations = ChatConversation::where('doctor_id', $doctor->id)
            ->with(['patient.user', 'messages' => fn ($q) => $q->latest()->take(1)])
            ->latest('last_message_at')
            ->get();

        $conversation->load(['patient.user', 'messages' => fn ($q) => $q->oldest()]);

        return view('doctor.chat.index', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
        ]);
    }

    /**
     * Send a consultation response message.
     */
    public function sendMessage(Request $request, ChatConversation $conversation): RedirectResponse
    {
        $doctor = Auth::user()->doctor;

        if ($conversation->doctor_id !== $doctor->id) {
            abort(403, 'Access denied. You cannot post messages to this consultation channel.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'sender_role' => 'doctor',
            'message' => $validated['message'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        return redirect()->route('doctor.chat.show', $conversation->id);
    }
}
