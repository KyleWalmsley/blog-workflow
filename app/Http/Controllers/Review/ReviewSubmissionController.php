<?php

namespace App\Http\Controllers\Review;

use App\Enums\BlogStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Review\UpdateBlogReviewRequest;
use App\Models\Blog;
use App\Models\Job;
use App\Services\ReviewSubmissionService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewSubmissionController extends Controller
{
    public function __construct(
        private ReviewSubmissionService $reviewSubmissionService,
    ) {}

    public function update(UpdateBlogReviewRequest $request, string $token, Blog $blog): JsonResponse
    {
        $job = $this->findJob($token);

        abort_unless($blog->job_id === $job->id, 404);

        try {
            $status = BlogStatus::from($request->validated('status'));
            $this->reviewSubmissionService->updateBlogReview(
                $blog,
                $status,
                $request->validated('client_notes')
            );
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $blog->refresh();
        $job->refresh()->load('blogs');

        $reviewedCount = $job->blogs->filter(fn ($b) => $b->status->value !== 'pending')->count();

        return response()->json([
            'message' => 'Review saved.',
            'blog' => [
                'id' => $blog->id,
                'status' => $blog->status->value,
                'client_notes' => $blog->client_notes,
            ],
            'reviewed_count' => $reviewedCount,
            'total_count' => $job->blogs->count(),
            'all_reviewed' => $job->allBlogsReviewed(),
        ]);
    }

    public function submit(Request $request, string $token): JsonResponse|RedirectResponse
    {
        $job = $this->findJob($token);

        try {
            $result = $this->reviewSubmissionService->submitReview($job);
        } catch (DomainException $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return back()->with('success', $result['message']);
    }

    public function finalize(Request $request, string $token): JsonResponse|RedirectResponse
    {
        $job = $this->findJob($token);

        try {
            $this->reviewSubmissionService->finalize($job);
        } catch (DomainException $e) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }

        $message = 'Thank you! Your job has been finalised and completed.';

        if ($request->expectsJson()) {
            return response()->json(['message' => $message, 'completed' => true]);
        }

        return back()->with('success', $message);
    }

    private function findJob(string $token): Job
    {
        return Job::where('review_token', $token)->firstOrFail();
    }
}
