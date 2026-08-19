<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\CreateConversationRequest;
use App\Http\Requests\Chat\CreateMessageRequest;
use App\Http\Resources\V1\ChatConversationResource;
use App\Http\Resources\V1\ChatMessageResource;
use App\Models\AuditLog;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ChatController extends Controller
{
    /**
     * List chat conversations for the authenticated participant.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = ChatConversation::query()
            ->with(['doctor.specialty', 'doctor.user', 'patient.user', 'messages' => fn ($q) => $q->latest()->limit(1)]);

        if ($user->isPatient()) {
            $patient = $user->patient ?? Patient::create(['user_id' => $user->id]);
            $query->where('patient_id', $patient->id);
        } elseif ($user->isDoctor()) {
            $doctor = $user->doctor;
            if ($doctor) {
                $query->where('doctor_id', $doctor->id);
            }
        }

        $perPage = min((int) $request->query('per_page', 15), 50);
        $conversations = $query->latest('last_message_at')->paginate($perPage);

        return $this->paginatedResponse(
            ChatConversationResource::collection($conversations),
            'Chat conversations retrieved.'
        );
    }

    /**
     * Create or retrieve an existing consultation chat conversation channel.
     */
    public function store(CreateConversationRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $patient = $user->patient ?? Patient::create(['user_id' => $user->id]);
        $doctor = Doctor::findOrFail($validated['doctor_id']);

        $conversation = DB::transaction(function () use ($validated, $patient, $doctor, $user, $request) {
            $conversation = ChatConversation::firstOrCreate([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_id' => $validated['appointment_id'] ?? null,
            ], [
                'status' => 'active',
                'last_message_at' => now(),
            ]);

            if (! empty($validated['initial_message'])) {
                ChatMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $user->id,
                    'message' => $validated['initial_message'],
                    'message_type' => 'text',
                    'created_at' => now(),
                ]);
                $conversation->update(['last_message_at' => now()]);
            }

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'INITIATE_CHAT_CONVERSATION',
                'entity_type' => 'ChatConversation',
                'entity_id' => $conversation->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return $conversation->load(['doctor.specialty', 'doctor.user', 'patient.user', 'messages.sender']);
        });

        return $this->successResponse(
            new ChatConversationResource($conversation),
            'Chat conversation initiated.',
            201
        );
    }

    /**
     * Retrieve conversation details.
     */
    public function show(ChatConversation $conversation): JsonResponse
    {
        Gate::authorize('view', $conversation);

        $conversation->load(['doctor.specialty', 'doctor.user', 'patient.user']);

        return $this->successResponse(
            new ChatConversationResource($conversation),
            'Chat conversation details retrieved.'
        );
    }

    /**
     * Retrieve paginated messages within a conversation.
     */
    public function messages(ChatConversation $conversation, Request $request): JsonResponse
    {
        Gate::authorize('view', $conversation);

        $perPage = min((int) $request->query('per_page', 30), 100);
        $messages = $conversation->messages()
            ->with('sender')
            ->latest('created_at')
            ->paginate($perPage);

        return $this->paginatedResponse(
            ChatMessageResource::collection($messages),
            'Chat messages retrieved.'
        );
    }

    /**
     * Send a new message inside a conversation channel.
     */
    public function sendMessage(CreateMessageRequest $request, ChatConversation $conversation): JsonResponse
    {
        Gate::authorize('sendMessage', $conversation);

        $user = $request->user();
        $validated = $request->validated();

        $message = DB::transaction(function () use ($validated, $conversation, $user) {
            $msg = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'message' => $validated['message'],
                'message_type' => $validated['message_type'] ?? 'text',
                'attachment_path' => $validated['attachment_path'] ?? null,
                'created_at' => now(),
            ]);

            $conversation->update(['last_message_at' => now()]);

            return $msg->load('sender');
        });

        return $this->successResponse(
            new ChatMessageResource($message),
            'Message sent successfully.',
            201
        );
    }
}
