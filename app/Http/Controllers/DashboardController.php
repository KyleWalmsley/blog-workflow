<?php

namespace App\Http\Controllers;

use App\Enums\JobStatus;
use App\Models\Job;
use App\Services\NotificationService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function index(): View
    {
        $stats = [
            'active_clients' => \App\Models\Client::active()->count(),
            'draft_jobs' => Job::where('status', JobStatus::Draft)->count(),
            'in_review_jobs' => Job::where('status', JobStatus::InReview)->count(),
            'completed_jobs' => Job::where('status', JobStatus::Completed)->count(),
        ];

        $recentJobs = Job::with('client')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentJobs' => $recentJobs,
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }
}
