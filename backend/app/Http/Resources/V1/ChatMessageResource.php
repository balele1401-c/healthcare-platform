<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_id' => $this->sender_id,
            'sender_name' => $this->whenLoaded('sender', fn () => $this->sender->name),
            'sender_role' => $this->whenLoaded('sender', fn () => $this->sender->role?->value ?? (string) $this->sender->role),
            'message' => $this->message,
            'message_type' => $this->message_type,
            'attachment_path' => $this->attachment_path,
            'is_mine' => $request->user() ? $this->sender_id === $request->user()->id : false,
            'read_at' => $this->read_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
