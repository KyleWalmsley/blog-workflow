@extends('layouts.review')

@section('content')
<div
    x-data="reviewPortal({
        blogs: @js($job->blogs->map(fn($b) => [
            'id' => $b->id,
            'title' => $b->title,
            'status' => $b->status->value,
            'client_notes' => $b->client_notes,
            'focus_keyword' => $b->focus_keyword,
            'focus_location' => $b->focus_location,
            'meta_title' => $b->meta_title,
            'meta_description' => $b->meta_description,
            'content' => $b->content,
            'open' => false,
        ])),
        token: @js($job->review_token),
        csrf: @js(csrf_token()),
        reviewedCount: @js($reviewedCount),
        totalCount: @js($totalCount),
    })"
    @contextmenu.prevent
>
    <header class="review-header">
        <div class="review-header-inner">
            @if($client->logo_url)
                <img src="{{ $client->logo_url }}" alt="{{ $client->name }}" class="client-logo">
            @else
                <div class="client-logo-placeholder">{{ strtoupper(substr($client->name, 0, 1)) }}</div>
            @endif
            <div>
                <h1 class="review-title">{{ $job->title }}</h1>
                <p class="review-subtitle">{{ $client->name }} · Revision {{ $job->revision_count + 1 }} of {{ $job->maxRevisions() }}</p>
            </div>
        </div>
    </header>

    <nav class="review-sidebar">
        <p class="review-sidebar-heading">Contents</p>
        @foreach($job->blogs->sortBy('sort_order')->values() as $i => $sidebarBlog)
            <a href="#blog-{{ $i }}" class="review-sidebar-link">Article {{ $i + 1 }} — {{ $sidebarBlog->title }}</a>
        @endforeach
    </nav>

    <main class="review-main">
        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">{{ session('error') }}</div>
        @endif

        @if($job->status === \App\Enums\JobStatus::Completed)
            <div class="flash flash-success">This job has been completed. Thank you!</div>
        @endif

        <template x-if="completed">
            <div class="flash flash-success" x-text="thankYouMessage"></div>
        </template>

        <template x-if="!completed">
            <div>
                @if($job->revision_count >= $job->maxRevisions())
                    @include('review.partials.revision-notice')
                @endif

                @include('review.partials.progress-bar')

                <div class="blog-accordion">
                    <template x-for="(blog, index) in blogs" :key="blog.id">
                        <div class="blog-card" :id="'blog-' + index">
                            <div class="blog-card-header" @click="toggleBlog(index)">
                                <div>
                                    <div class="blog-card-title" x-text="blog.title"></div>
                                    <div class="blog-card-meta">
                                        <template x-if="blog.focus_keyword">
                                            <div class="meta-chip-row">
                                                <span class="meta-chip-label">Keywords</span>
                                                <span class="chip" x-text="blog.focus_keyword"></span>
                                            </div>
                                        </template>
                                        <template x-if="blog.focus_location">
                                            <div class="meta-chip-row">
                                                <span class="meta-chip-label">Location</span>
                                                <span class="chip" x-text="blog.focus_location"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <span
                                    class="status-pill"
                                    :class="{
                                        'status-pending': blog.status === 'pending',
                                        'status-approved': blog.status === 'approved',
                                        'status-declined': blog.status === 'declined',
                                    }"
                                    x-text="blog.status.charAt(0).toUpperCase() + blog.status.slice(1)"
                                ></span>
                            </div>
                            <div class="blog-card-body" x-show="blog.open" style="display: none;" x-bind:style="blog.open ? 'display: block;' : 'display: none;'">
                                <div class="blog-meta-grid">
                                    <div class="meta-item" x-show="blog.meta_title">
                                        <label>Meta Title</label>
                                        <span x-text="blog.meta_title"></span>
                                    </div>
                                    <div class="meta-item" x-show="blog.meta_description">
                                        <label>Meta Description</label>
                                        <span x-text="blog.meta_description"></span>
                                    </div>
                                </div>
                                <div class="blog-content" x-html="blog.content"></div>
                                @if($job->status === \App\Enums\JobStatus::InReview)
                                <div class="notes-area">
                                    <label>Feedback (required if declining)</label>
                                    <textarea
                                        x-model="blog.client_notes"
                                        placeholder="Tell us what needs to change..."
                                        @blur="saveNotes(blog)"
                                        @input="blog.declineError = false"
                                    ></textarea>
                                </div>
                                <div class="review-actions">
                                    <button
                                        type="button"
                                        class="btn-approve"
                                        :class="{ 'selected': blog.status === 'approved' }"
                                        @click="setStatus(blog, 'approved')"
                                    >Approve</button>
                                    <button
                                        type="button"
                                        class="btn-decline"
                                        :class="{ 'selected': blog.status === 'declined' }"
                                        @click="setStatus(blog, 'declined')"
                                    >Decline</button>
                                    <span class="decline-error" x-show="blog.declineError" x-cloak>Please tell us the reason so we can improve this article</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
    </main>

    @if($job->status === \App\Enums\JobStatus::InReview)
    <footer class="review-footer" x-show="!completed">
        <div class="review-footer-inner">
            <span style="font-size: 13px; color: var(--text2);" x-text="reviewedCount + ' of ' + totalCount + ' articles reviewed'"></span>
            <button type="button" class="btn-submit" :disabled="!allReviewed || submitting" @click="submitReview()">
                <span x-show="!submitting">Submit Review</span>
                <span x-show="submitting">Submitting...</span>
            </button>
        </div>
    </footer>
    @endif

    @include('review.modals.submit-blocked')
    @include('review.modals.finalize-job')
</div>
@endsection
