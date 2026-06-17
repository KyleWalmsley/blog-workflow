<?php

namespace App\Services;

use App\Enums\NotificationType;
use App\Models\AdminNotification;
use App\Models\Job;

class NotificationService
{
    public function notify(NotificationType $type, Job $job, string $message): AdminNotification
    {
        return AdminNotification::create([
            'type' => $type,
            'message' => $message,
            'job_id' => $job->id,
        ]);
    }

    public function unreadCount(): int
    {
        return AdminNotification::unread()->count();
    }
}
