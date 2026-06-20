<?php

namespace App\Enums;

enum JobType: string
{
    case BlogCreation = 'blog_creation';
    case WebsiteCopywriting = 'website_copywriting';

    public function label(): string
    {
        return match ($this) {
            self::BlogCreation => 'Blog Creation',
            self::WebsiteCopywriting => 'Website Copywriting',
        };
    }
}
