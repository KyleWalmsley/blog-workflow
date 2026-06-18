<?php

namespace App\Enums;

enum OutgoingEmailType: string
{
    case ReviewInvitation = 'review_invitation';
    case Reminder = 'reminder';
    case Completion = 'completion';

    public function label(): string
    {
        return match ($this) {
            self::ReviewInvitation => 'Review Invitation',
            self::Reminder => 'Reminder',
            self::Completion => 'Completion',
        };
    }
}
