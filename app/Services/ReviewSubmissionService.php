<?php

namespace App\Services;

use App\Enums\BlogStatus;
use App\Enums\JobStatus;
use App\Enums\NotificationType;
use App\Models\Blog;
use App\Models\Job;
use DomainException;

class ReviewSubmissionService
{
    public function __construct(
        private NotificationService $notificationService,
        private JobWorkflowService $jobWorkflowService,
    ) {}

    public function updateBlogReview(Blog $blog, BlogStatus $status, ?string $notes): void
    {
        $job = $blog->job;

        if ($job->status === JobStatus::Completed) {
            throw new DomainException('This job has already been completed.');
        }

        if ($job->status !== JobStatus::InReview) {
            throw new DomainException('This job is not currently open for review.');
        }

        if ($status === BlogStatus::Declined && ! $job->canStartRevisionCycle()) {
            throw new DomainException('The revision limit has been reached. Please contact your account manager.');
        }

        $blog->update([
            'status' => $status,
            'client_notes' => $status === BlogStatus::Declined ? $notes : null,
        ]);
    }

    public function submitReview(Job $job): array
    {
        if ($job->status !== JobStatus::InReview) {
            throw new DomainException('This job is not currently open for review.');
        }

        if (! $job->allBlogsReviewed()) {
            throw new DomainException('Please review all articles before submitting.');
        }

        $job->update(['review_submitted_at' => now()]);

        if ($job->allBlogsApproved()) {
            return [
                'needs_finalization' => true,
                'message' => 'All articles approved. Please confirm to finalise this job.',
            ];
        }

        $job->incrementRevisionCount();
        $job->refresh();

        if ($job->revision_count >= $job->maxRevisions()) {
            $this->notificationService->notify(
                NotificationType::RevisionLimitReached,
                $job,
                "Revision limit reached for job \"{$job->title}\" ({$job->client->name})."
            );

            return [
                'needs_finalization' => false,
                'revision_limit_reached' => true,
                'message' => 'Thank you. This was your final review cycle. Your account manager will follow up.',
            ];
        }

        $this->notificationService->notify(
            NotificationType::ReviewSubmitted,
            $job,
            "Client submitted review for job \"{$job->title}\" ({$job->client->name}) with declined articles."
        );

        return [
            'needs_finalization' => false,
            'revision_limit_reached' => false,
            'message' => 'Thank you. Your feedback has been submitted. Revised articles will be sent for another review.',
        ];
    }

    public function finalize(Job $job): void
    {
        if (! $job->allBlogsApproved()) {
            throw new DomainException('All articles must be approved before finalising.');
        }

        $this->jobWorkflowService->finalize($job);
    }
}
