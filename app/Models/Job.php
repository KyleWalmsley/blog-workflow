<?php

namespace App\Models;

use App\Enums\BlogStatus;
use App\Enums\JobStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Job extends Model
{
    protected $fillable = [
        'client_id',
        'title',
        'status',
        'revision_count',
        'review_token',
        'review_submitted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => JobStatus::class,
            'revision_count' => 'integer',
            'review_submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Job $job) {
            if (empty($job->review_token)) {
                $job->review_token = Str::random(64);
            }

            if (empty($job->status)) {
                $job->status = JobStatus::Draft;
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class)->orderBy('sort_order');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AdminNotification::class);
    }

    public function reviewUrl(): string
    {
        return route('review.show', $this->review_token);
    }

    public function allBlogsReviewed(): bool
    {
        return ! $this->blogs()->where('status', BlogStatus::Pending)->exists();
    }

    public function allBlogsApproved(): bool
    {
        $total = $this->blogs()->count();

        if ($total === 0) {
            return false;
        }

        return $this->blogs()->where('status', BlogStatus::Approved)->count() === $total;
    }

    public function hasDeclinedBlogs(): bool
    {
        return $this->blogs()->where('status', BlogStatus::Declined)->exists();
    }

    public function canStartRevisionCycle(): bool
    {
        $maxRevisions = (int) config('blog-workflow.max_revisions', 2);

        return $this->revision_count < $maxRevisions
            && $this->status !== JobStatus::Completed;
    }

    public function maxRevisions(): int
    {
        return (int) config('blog-workflow.max_revisions', 2);
    }

    public function incrementRevisionCount(): void
    {
        $this->increment('revision_count');
        $this->refresh();
    }
}
