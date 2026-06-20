@extends('layouts.admin')

@section('title', $job->title)
@section('page-title', $job->title)

@section('content')
    <div class="page-header">
        <div>
            <h2 class="text-base font-semibold text-neutral-900">{{ $job->title }}</h2>
            <p class="text-xs text-neutral-500 mt-1 flex items-center gap-2">
                {{ $job->client->name }} ·
                @include('admin.partials.status-badge', ['status' => $job->status])
                · Revision {{ $job->revision_count }} of {{ $job->maxRevisions() }}
            </p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-muted">Edit</a>
            @if($job->status === \App\Enums\JobStatus::Draft)
                <form method="POST" action="{{ route('admin.jobs.send-for-review', $job) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-brand">Send for Review</button>
                </form>
            @endif
            @if($job->status === \App\Enums\JobStatus::InReview)
                <form method="POST" action="{{ route('admin.jobs.prepare-re-review', $job) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-muted">Prepare Re-Review</button>
                </form>
                <form method="POST" action="{{ route('admin.jobs.complete', $job) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-brand">Complete</button>
                </form>
            @endif
            @if($job->status === \App\Enums\JobStatus::Completed)
                <a href="{{ route('admin.jobs.export', $job) }}" class="btn btn-brand">Export ZIP</a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-2 gap-5">
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-7">
            <h3 class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-3">Client</h3>
            <p class="text-sm text-neutral-600">
                <a href="{{ route('admin.clients.show', $job->client) }}" class="text-blue-600 hover:underline">{{ $job->client->name }}</a>
            </p>
            @if($job->client->business_description)
                <p class="mt-2 text-xs text-neutral-400">{{ Str::limit($job->client->business_description, 200) }}</p>
            @endif
        </div>
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-7">
            <h3 class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-1">Review Link</h3>
            <p class="text-xs text-neutral-400 mb-3">Share with client for article review</p>
            <div style="display: flex; gap: 8px; align-items: center;">
                <input type="text" readonly class="form-input" value="{{ $job->reviewUrl() }}" id="review-url" style="font-size: 11px; font-family: 'DM Mono', monospace;">
                <button type="button" class="btn btn-sm btn-brand" onclick="navigator.clipboard.writeText(document.getElementById('review-url').value); this.textContent='Copied!'; setTimeout(() => this.textContent='Copy', 2000);">Copy</button>
            </div>
            @if($job->review_submitted_at)
                <p class="mt-2 text-xs text-neutral-400">Last submitted {{ $job->review_submitted_at->diffForHumans() }}</p>
            @endif
        </div>
    </div>

    @if($job->isCopywriting())
    {{-- Website Copywriting sections --}}
    <div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-6 border-b border-neutral-100 flex items-center justify-between">
            <div>
                <h3 class="text-xs font-semibold text-neutral-500 uppercase tracking-wide">Copy Sections</h3>
                <p class="text-sm text-neutral-400 mt-1.5">
                    {{ $sectionCounts['pending'] }} pending ·
                    {{ $sectionCounts['approved'] }} approved ·
                    {{ $sectionCounts['declined'] }} declined
                </p>
            </div>
            <a href="{{ route('admin.jobs.copy-sections.create', $job) }}" class="btn btn-brand">Add Section</a>
        </div>

        @if($job->copySections->isEmpty())
            <div class="empty-state"><p>No sections yet. Add copy sections before sending for review.</p></div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-100 bg-neutral-50">
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Type</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Title / Headline</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach($job->copySections->sortBy(fn($s) => $s->section_type->sortOrder()) as $section)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-6 py-4 text-neutral-500 text-xs">{{ $section->section_type->label() }}</td>
                            <td class="px-6 py-4 font-medium text-neutral-800">
                                {{ $section->title ?? $section->headline ?? '—' }}
                            </td>
                            <td class="px-6 py-4">@include('admin.partials.status-badge', ['status' => $section->status])</td>
                            <td class="px-6 py-4">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    <a href="{{ route('admin.jobs.copy-sections.edit', [$job, $section]) }}" class="btn btn-sm btn-muted">Edit</a>
                                    <form method="POST" action="{{ route('admin.jobs.copy-sections.destroy', [$job, $section]) }}" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" onclick="openDeleteModal(this.closest('form'))">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    @else
    {{-- Blog Creation articles --}}
    <div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-6 border-b border-neutral-100 flex items-center justify-between">
            <div>
                <h3 class="text-xs font-semibold text-neutral-500 uppercase tracking-wide">Blog Articles</h3>
                <p class="text-sm text-neutral-400 mt-1.5">
                    {{ $blogCounts['pending'] }} pending ·
                    {{ $blogCounts['approved'] }} approved ·
                    {{ $blogCounts['declined'] }} declined
                </p>
            </div>
            <a href="{{ route('admin.jobs.blogs.create', $job) }}" class="btn btn-brand">Add Article</a>
        </div>

        @if($job->blogs->isEmpty())
            <div class="empty-state"><p>No articles yet. Add blog content before sending for review.</p></div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-100 bg-neutral-50">
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">#</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Title</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Focus</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach($job->blogs as $blog)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-6 py-4 text-neutral-400 font-mono text-xs">{{ $blog->sort_order + 1 }}</td>
                            <td class="px-6 py-4 font-medium text-neutral-800">{{ $blog->title }}</td>
                            <td class="px-6 py-4 text-neutral-500 text-xs">{{ $blog->focus_keyword ?? '—' }}{{ $blog->focus_location ? ' · ' . $blog->focus_location : '' }}</td>
                            <td class="px-6 py-4">@include('admin.partials.status-badge', ['status' => $blog->status])</td>
                            <td class="px-6 py-4">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    <a href="{{ route('admin.jobs.blogs.edit', [$job, $blog]) }}" class="btn btn-sm btn-muted">Edit</a>
                                    <form method="POST" action="{{ route('admin.jobs.blogs.destroy', [$job, $blog]) }}" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" onclick="openDeleteModal(this.closest('form'))">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    @endif

    @if($job->outgoingEmails->isNotEmpty())
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-7">
            <h3 class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-4">Activity</h3>
            <div class="space-y-1">
                @foreach($job->outgoingEmails as $email)
                    <div class="flex items-center gap-3 py-4 border-b border-neutral-100 last:border-0">
                        <span class="flex-shrink-0 w-2 h-2 rounded-full {{ $email->status->value === 'sent' ? 'bg-green-500' : 'bg-red-400' }}"></span>
                        <span class="text-sm text-neutral-700">
                            {{ $email->type->label() }} email
                            @if($email->status->value === 'sent')
                                sent to <strong class="font-medium">{{ $email->recipient_email }}</strong>
                            @else
                                failed for <strong class="font-medium">{{ $email->recipient_email }}</strong>
                            @endif
                        </span>
                        <span class="ml-auto text-xs text-neutral-400 whitespace-nowrap">
                            {{ ($email->sent_at ?? $email->created_at)->format('H:i \o\n d M Y') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Delete confirmation modal --}}
    <div id="delete-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:200; align-items:center; justify-content:center; padding:20px;">
        <div style="background:white; border-radius:14px; padding:32px; max-width:400px; width:100%; box-shadow:0 20px 60px rgba(0,0,0,0.18);">
            <h3 style="font-family:'DM Serif Display',serif; font-size:1.35rem; color:#1a1a2e; margin-bottom:10px;">Are you sure you wish to proceed?</h3>
            <p style="color:#666; font-size:14px; margin-bottom:28px;">This action cannot be undone.</p>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="closeDeleteModal()" class="btn btn-muted">Cancel</button>
                <button type="button" id="delete-confirm-btn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let pendingDeleteForm = null;
        function openDeleteModal(form) {
            pendingDeleteForm = form;
            const modal = document.getElementById('delete-modal');
            modal.style.display = 'flex';
        }
        function closeDeleteModal() {
            pendingDeleteForm = null;
            document.getElementById('delete-modal').style.display = 'none';
        }
        document.getElementById('delete-confirm-btn').addEventListener('click', function () {
            if (pendingDeleteForm) pendingDeleteForm.submit();
        });
        document.getElementById('delete-modal').addEventListener('click', function (e) {
            if (e.target === this) closeDeleteModal();
        });
    </script>
    @endpush
@endsection
