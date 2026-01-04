<?php

namespace App\Domain\Dashboard\Services;

use App\Models\AlertNotification;

/**
 * Service responsible for notification analysis
 */
class NotificationAnalysisService
{
    /**
     * Get unread notifications count
     */
    public function getUnreadNotificationsCount(string $userId): int
    {
        return AlertNotification::where('user_id', $userId)
            ->unread()
            ->count();
    }

    /**
     * Get unread notifications (last 5)
     */
    public function getUnreadNotifications(string $userId): array
    {
        return AlertNotification::where('user_id', $userId)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->uuid,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            })
            ->toArray();
    }
}
