<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BlogStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJobRequest;
use App\Http\Requests\Admin\UpdateJobRequest;
use App\Models\Client;
use App\Models\Job;
use App\Services\JobWorkflowService;
use App\Services\NotificationService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JobController extends Controller
{
    public function __construct(
        private JobWorkflowService $jobWorkflowService,
        private NotificationService $notificationService,
    ) {}

    public function index(): View
    {
        $jobs = Job::with('client')->latest()->paginate(15);

        return view('admin.jobs.index', [
            'jobs' => $jobs,
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }

    public function create(): View
    {
        $clients = Client::active()->orderBy('name')->get();

        return view('admin.jobs.create', [
            'clients' => $clients,
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }

    public function store(StoreJobRequest $request): RedirectResponse
    {
        $job = Job::create($request->validated());

        return redirect()
            ->route('admin.jobs.show', $job)
            ->with('success', 'Job created successfully.');
    }

    public function show(Job $job): View
    {
        $job->load(['client', 'blogs']);

        $blogCounts = [
            'pending' => $job->blogs->where('status', BlogStatus::Pending)->count(),
            'approved' => $job->blogs->where('status', BlogStatus::Approved)->count(),
            'declined' => $job->blogs->where('status', BlogStatus::Declined)->count(),
        ];

        return view('admin.jobs.show', [
            'job' => $job,
            'blogCounts' => $blogCounts,
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }

    public function edit(Job $job): View
    {
        $clients = Client::active()->orderBy('name')->get();

        return view('admin.jobs.edit', [
            'job' => $job,
            'clients' => $clients,
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }

    public function update(UpdateJobRequest $request, Job $job): RedirectResponse
    {
        $job->update($request->validated());

        return redirect()
            ->route('admin.jobs.show', $job)
            ->with('success', 'Job updated successfully.');
    }

    public function destroy(Job $job): RedirectResponse
    {
        $job->delete();

        return redirect()
            ->route('admin.jobs.index')
            ->with('success', 'Job deleted successfully.');
    }

    public function sendForReview(Job $job): RedirectResponse
    {
        try {
            $this->jobWorkflowService->sendForReview($job);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Job sent for client review.');
    }

    public function prepareReReview(Job $job): RedirectResponse
    {
        try {
            $this->jobWorkflowService->prepareReReview($job);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Declined articles reset to pending for re-review.');
    }

    public function complete(Job $job): RedirectResponse
    {
        try {
            $this->jobWorkflowService->complete($job);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Job marked as completed.');
    }
}
