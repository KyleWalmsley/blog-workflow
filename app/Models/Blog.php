<?php

namespace App\Models;

use App\Enums\BlogStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
{
    protected $fillable = [
        'job_id',
        'sort_order',
        'title',
        'content',
        'meta_title',
        'meta_description',
        'focus_keyword',
        'focus_location',
        'status',
        'client_notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => BlogStatus::class,
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Blog $blog) {
            if ($blog->sort_order === null) {
                $max = static::where('job_id', $blog->job_id)->max('sort_order');
                $blog->sort_order = ($max ?? -1) + 1;
            }

            if (empty($blog->status)) {
                $blog->status = BlogStatus::Pending;
            }
        });
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
