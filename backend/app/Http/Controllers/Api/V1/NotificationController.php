<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * List in-app notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->notifications()->latest();

        if ($request->has('read')) {
            $isRead = filter_var($request->query('read'), FILTER_VALIDATE_BOOLEAN);
            $query->where(fn ($q) => $isRead ? $q->whereNotNull('read_at') : $q->whereNull('read_at'));
        }

        $perPage = min((int) $request->query('per_page', 20), 50);
        $notifications = $query->paginate($perPage);

        return $this->paginatedResponse(
            NotificationResource::collection($notifications),
            'Notifications retrieved.'
        );
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Notification $notification, Request $request): JsonResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized to update this notification.', 403);
        }

        $notification->update(['read_at' => now()]);

        return $this->successResponse(
            new NotificationResource($notification),
            'Notification marked as read.'
        );
    }

    /**
     * Mark all notifications for the authenticated user as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $updatedCount = $request->user()->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->successResponse(
            ['updated_count' => $updatedCount],
            'All unread notifications marked as read.'
        );
    }
}
