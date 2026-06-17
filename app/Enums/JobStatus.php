<?php

namespace App\Enums;

enum JobStatus: string
{
    case Draft = 'draft';
    case InReview = 'in_review';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::InReview => 'In Review',
            self::Completed => 'Completed',
        };
    }
}
