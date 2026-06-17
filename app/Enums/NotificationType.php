<?php

namespace App\Enums;

enum NotificationType: string
{
    case ReviewSubmitted = 'review_submitted';
    case JobCompleted = 'job_completed';
    case RevisionLimitReached = 'revision_limit_reached';

    public function label(): string
    {
        return match ($this) {
            self::ReviewSubmitted => 'Review Submitted',
            self::JobCompleted => 'Job Completed',
            self::RevisionLimitReached => 'Revision Limit Reached',
        };
    }
}
