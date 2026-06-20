<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CopySectionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCopySectionRequest;
use App\Http\Requests\Admin\UpdateCopySectionRequest;
use App\Models\CopySection;
use App\Models\Job;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CopySectionController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function create(Job $job): View
    {
        return view('admin.copy-sections.create', [
            'job' => $job,
            'sectionTypes' => CopySectionType::cases(),
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }

    public function store(StoreCopySectionRequest $request, Job $job): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['sort_order']) && $data['sort_order'] === null) {
            unset($data['sort_order']);
        }

        $job->copySections()->create($data);

        return redirect()
            ->route('admin.jobs.show', $job)
            ->with('success', 'Section added successfully.');
    }

    public function edit(Job $job, CopySection $copySection): View
    {
        abort_unless($copySection->job_id === $job->id, 404);

        return view('admin.copy-sections.edit', [
            'job' => $job,
            'section' => $copySection,
            'sectionTypes' => CopySectionType::cases(),
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }

    public function update(UpdateCopySectionRequest $request, Job $job, CopySection $copySection): RedirectResponse
    {
        abort_unless($copySection->job_id === $job->id, 404);

        $copySection->update($request->validated());

        return redirect()
            ->route('admin.jobs.show', $job)
            ->with('success', 'Section updated successfully.');
    }

    public function destroy(Job $job, CopySection $copySection): RedirectResponse
    {
        abort_unless($copySection->job_id === $job->id, 404);

        $copySection->delete();

        return redirect()
            ->route('admin.jobs.show', $job)
            ->with('success', 'Section deleted.');
    }
}
