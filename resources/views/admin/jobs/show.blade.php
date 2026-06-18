@extends('layouts.admin')

@section('title', $job->title)
@section('page-title', $job->title)

@section('content')
    <div class="page-header">
        <div>
            <h2 class="card-title">{{ $job->title }}</h2>
            <p class="card-sub">
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

    <div class="grid g2">
        <div class="card">
            <h3 class="card-title">Client</h3>
            <p style="margin-top: 8px; font-size: 13px; color: var(--text2);">
                <a href="{{ route('admin.clients.show', $job->client) }}">{{ $job->client->name }}</a>
            </p>
            @if($job->client->business_description)
                <p style="margin-top: 8px; font-size: 12px; color: var(--text3);">{{ Str::limit($job->client->business_description, 200) }}</p>
            @endif
        </div>
        <div class="card">
            <h3 class="card-title">Review Link</h3>
            <p class="card-sub" style="margin-bottom: 12px;">Share with client for article review</p>
            <div style="display: flex; gap: 8px; align-items: center;">
                <input type="text" readonly class="form-input" value="{{ $job->reviewUrl() }}" id="review-url" style="font-size: 11px; font-family: 'DM Mono', monospace;">
                <button type="button" class="btn btn-sm btn-brand" onclick="navigator.clipboard.writeText(document.getElementById('review-url').value); this.textContent='Copied!'; setTimeout(() => this.textContent='Copy', 2000);">Copy</button>
            </div>
            @if($job->review_submitted_at)
                <p style="margin-top: 10px; font-size: 12px; color: var(--text3);">Last submitted {{ $job->review_submitted_at->diffForHumans() }}</p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="page-header">
            <div>
                <h3 class="card-title">Blog Articles</h3>
                <p class="card-sub">
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
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Focus</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($job->blogs as $blog)
                            <tr>
                                <td>{{ $blog->sort_order + 1 }}</td>
                                <td>{{ $blog->title }}</td>
                                <td>{{ $blog->focus_keyword ?? '—' }}{{ $blog->focus_location ? ' · ' . $blog->focus_location : '' }}</td>
                                <td>@include('admin.partials.status-badge', ['status' => $blog->status])</td>
                                <td style="display: flex; gap: 6px;">
                                    <a href="{{ route('admin.jobs.blogs.edit', [$job, $blog]) }}" class="btn btn-sm btn-muted">Edit</a>
                                    <form method="POST" action="{{ route('admin.jobs.blogs.destroy', [$job, $blog]) }}" onsubmit="return confirm('Delete this article?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @if($job->outgoingEmails->isNotEmpty())
        <div class="card">
            <h3 class="card-title">Activity</h3>
            <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 10px;">
                @foreach($job->outgoingEmails as $email)
                    <div style="display: flex; align-items: center; gap: 12px; font-size: 13px; padding: 10px 14px; background: var(--bg3); border-radius: 8px; border: 1px solid var(--border);">
                        <span style="flex-shrink: 0;">
                            @if($email->status->value === 'sent')
                                <span style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%; display: inline-block;"></span>
                            @else
                                <span style="width: 8px; height: 8px; background: var(--rose); border-radius: 50%; display: inline-block;"></span>
                            @endif
                        </span>
                        <span style="color: var(--text);">
                            {{ $email->type->label() }} email
                            @if($email->status->value === 'sent')
                                sent to <strong>{{ $email->recipient_email }}</strong>
                            @else
                                failed for <strong>{{ $email->recipient_email }}</strong>
                            @endif
                        </span>
                        <span style="margin-left: auto; white-space: nowrap; color: var(--text2); font-size: 12px;">
                            {{ ($email->sent_at ?? $email->created_at)->format('H:i \o\n d M Y') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection
