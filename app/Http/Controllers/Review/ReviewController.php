<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function show(string $token): View
    {
        $job = Job::where('review_token', $token)
            ->with(['client', 'blogs', 'copySections'])
            ->firstOrFail();

        if ($job->status === \App\Enums\JobStatus::Completed) {
            return view('review.completed', [
                'job' => $job,
                'client' => $job->client,
            ]);
        }

        if ($job->status !== \App\Enums\JobStatus::InReview) {
            return view('review.unavailable', [
                'job' => $job,
                'client' => $job->client,
            ]);
        }

        if ($job->isCopywriting()) {
            $reviewedCount = $job->copySections->filter(fn ($s) => $s->status->value !== 'pending')->count();
            $totalCount = $job->copySections->count();
        } else {
            $reviewedCount = $job->blogs->filter(fn ($b) => $b->status->value !== 'pending')->count();
            $totalCount = $job->blogs->count();
        }

        return view('review.show', [
            'job' => $job,
            'client' => $job->client,
            'reviewedCount' => $reviewedCount,
            'totalCount' => $totalCount,
        ]);
    }
}
