<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogRequest;
use App\Http\Requests\Admin\UpdateBlogRequest;
use App\Models\Blog;
use App\Models\Job;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function create(Job $job): View
    {
        return view('admin.blogs.create', [
            'job' => $job,
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }

    public function store(StoreBlogRequest $request, Job $job): RedirectResponse
    {
        $job->blogs()->create($request->validated());

        return redirect()
            ->route('admin.jobs.show', $job)
            ->with('success', 'Blog article added.');
    }

    public function edit(Job $job, Blog $blog): View
    {
        abort_unless($blog->job_id === $job->id, 404);

        return view('admin.blogs.edit', [
            'job' => $job,
            'blog' => $blog,
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }

    public function update(UpdateBlogRequest $request, Job $job, Blog $blog): RedirectResponse
    {
        abort_unless($blog->job_id === $job->id, 404);

        $blog->update($request->validated());

        return redirect()
            ->route('admin.jobs.show', $job)
            ->with('success', 'Blog article updated.');
    }

    public function destroy(Job $job, Blog $blog): RedirectResponse
    {
        abort_unless($blog->job_id === $job->id, 404);

        $blog->delete();

        return redirect()
            ->route('admin.jobs.show', $job)
            ->with('success', 'Blog article deleted.');
    }
}
