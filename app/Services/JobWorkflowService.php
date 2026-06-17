<?php

namespace App\Services;

use App\Enums\BlogStatus;
use App\Enums\JobStatus;
use App\Enums\NotificationType;
use App\Models\Job;
use DomainException;

class JobWorkflowService
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function sendForReview(Job $job): void
    {
        if ($job->status !== JobStatus::Draft) {
            throw new DomainException('Only draft jobs can be sent for review.');
        }

        if ($job->blogs()->count() < 1) {
            throw new DomainException('Add at least one blog before sending for review.');
        }

        $job->update(['status' => JobStatus::InReview]);
    }

    public function prepareReReview(Job $job): void
    {
        if ($job->status !== JobStatus::InReview) {
            throw new DomainException('Only jobs in review can be prepared for re-review.');
        }

        $job->blogs()
            ->where('status', BlogStatus::Declined)
            ->update([
                'status' => BlogStatus::Pending,
                'client_notes' => null,
            ]);

        $job->update(['review_submitted_at' => null]);
    }

    public function complete(Job $job): void
    {
        if ($job->status === JobStatus::Completed) {
            throw new DomainException('This job is already completed.');
        }

        $job->update([
            'status' => JobStatus::Completed,
            'completed_at' => now(),
        ]);

        $this->notificationService->notify(
            NotificationType::JobCompleted,
            $job,
            "Job \"{$job->title}\" ({$job->client->name}) has been completed."
        );
    }

    public function finalize(Job $job): void
    {
        if ($job->status !== JobStatus::InReview) {
            throw new DomainException('Only jobs in review can be finalised.');
        }

        if (! $job->allBlogsApproved()) {
            throw new DomainException('All articles must be approved before finalising.');
        }

        $this->complete($job);
    }
}
