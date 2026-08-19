<?php

namespace App\Policies;

use App\Models\ChatConversation;
use App\Models\User;

class ChatConversationPolicy
{
    public function view(User $user, ChatConversation $conversation): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isDoctor()) {
            return $conversation->doctor && $conversation->doctor->user_id === $user->id;
        }

        if ($user->isPatient()) {
            return $conversation->patient && $conversation->patient->user_id === $user->id;
        }

        return false;
    }

    public function sendMessage(User $user, ChatConversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }
}
