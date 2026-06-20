@extends('layouts.review')

@section('content')
<div
    x-data="reviewPortal({
        jobType: @js($job->job_type->value),
        blogs: @js($job->isCopywriting() ? [] : $job->blogs->map(fn($b) => [
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
        sections: @js($job->isCopywriting() ? $job->copySections->sortBy(fn($s) => $s->section_type->sortOrder())->values()->map(fn($s) => [
            'id' => $s->id,
            'section_type' => $s->section_type->value,
            'section_label' => $s->section_type->label(),
            'title' => $s->title,
            'headline' => $s->headline,
            'sub_headline' => $s->sub_headline,
            'content' => $s->content,
            'meta_title' => $s->meta_title,
            'meta_description' => $s->meta_description,
            'status' => $s->status->value,
            'client_notes' => $s->client_notes,
            'open' => false,
        ]) : []),
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
        @if($job->isCopywriting())
            @foreach($job->copySections->sortBy(fn($s) => $s->section_type->sortOrder())->values() as $i => $sidebarSection)
                <a href="#section-{{ $i }}" class="review-sidebar-link">
                    {{ $sidebarSection->section_type->label() }}{{ $sidebarSection->title ? ' — ' . $sidebarSection->title : '' }}
                </a>
            @endforeach
        @else
            @foreach($job->blogs->sortBy('sort_order')->values() as $i => $sidebarBlog)
                <a href="#blog-{{ $i }}" class="review-sidebar-link">Article {{ $i + 1 }} — {{ $sidebarBlog->title }}</a>
            @endforeach
        @endif
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

                    {{-- Blog Creation --}}
                    <template x-if="!isCopywriting">
                        <div>
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
                                    <div class="blog-card-expand-hint" x-show="!blog.open" @click="toggleBlog(index)">
                                        <span>Click to expand</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06z" clip-rule="evenodd" /></svg>
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
                                            <button type="button" class="btn-approve" :class="{ 'selected': blog.status === 'approved' }" @click="setStatus(blog, 'approved')">Approve</button>
                                            <button type="button" class="btn-decline" :class="{ 'selected': blog.status === 'declined' }" @click="setStatus(blog, 'declined')">Decline</button>
                                            <span class="decline-error" x-show="blog.declineError" x-cloak>Please tell us the reason so we can improve this article</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Website Copywriting sections --}}
                    <template x-if="isCopywriting">
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <template x-for="(section, index) in sections" :key="section.id">
                                <div>
                                    {{-- Group heading — shown when section type changes --}}
                                    <h3
                                        class="section-group-heading"
                                        x-text="section.section_label"
                                        x-show="index === 0 || sections[index - 1].section_type !== section.section_type"
                                    ></h3>
                                <div class="blog-card" :id="'section-' + index">
                                    <div class="blog-card-header" @click="toggleSection(index)">
                                        <div>
                                            <div class="blog-card-title" x-text="section.section_label + (section.title ? ' — ' + section.title : '')"></div>
                                        </div>
                                        <span
                                            class="status-pill"
                                            :class="{
                                                'status-pending': section.status === 'pending',
                                                'status-approved': section.status === 'approved',
                                                'status-declined': section.status === 'declined',
                                            }"
                                            x-text="section.status.charAt(0).toUpperCase() + section.status.slice(1)"
                                        ></span>
                                    </div>
                                    <div class="blog-card-expand-hint" x-show="!section.open" @click="toggleSection(index)">
                                        <span>Click to expand</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06z" clip-rule="evenodd" /></svg>
                                    </div>
                                    <div class="blog-card-body" x-show="section.open" style="display: none;" x-bind:style="section.open ? 'display: block;' : 'display: none;'">

                                        {{-- Banner --}}
                                        <template x-if="section.section_type === 'banner'">
                                            <div class="copy-section-banner">
                                                <p class="copy-section-headline" x-text="section.headline"></p>
                                                <p class="copy-section-subheadline" x-text="section.sub_headline" x-show="section.sub_headline"></p>
                                            </div>
                                        </template>

                                        {{-- About Us: optional heading + content --}}
                                        <template x-if="section.section_type === 'about_us'">
                                            <div>
                                                <template x-if="section.headline">
                                                    <div>
                                                        <p class="section-subheading">Heading</p>
                                                        <p class="section-heading-value" x-text="section.headline"></p>
                                                    </div>
                                                </template>
                                                <p class="section-subheading">Content</p>
                                                <div class="blog-content" x-html="section.content"></div>
                                            </div>
                                        </template>

                                        {{-- About Page: content + meta --}}
                                        <template x-if="section.section_type === 'about_page'">
                                            <div>
                                                <p class="section-subheading">Content</p>
                                                <div class="blog-content" x-html="section.content"></div>
                                                <p class="section-subheading">Meta Data</p>
                                                <div class="blog-meta-grid">
                                                    <div class="meta-item" x-show="section.meta_title">
                                                        <label>Meta Title</label>
                                                        <span x-text="section.meta_title"></span>
                                                    </div>
                                                    <div class="meta-item" x-show="section.meta_description">
                                                        <label>Meta Description</label>
                                                        <span x-text="section.meta_description"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Service / Product: content + meta --}}
                                        <template x-if="section.section_type === 'service'">
                                            <div>
                                                <p class="section-subheading">Content</p>
                                                <div class="blog-content" x-html="section.content"></div>
                                                <p class="section-subheading">Meta Data</p>
                                                <div class="blog-meta-grid">
                                                    <div class="meta-item" x-show="section.meta_title">
                                                        <label>Meta Title</label>
                                                        <span x-text="section.meta_title"></span>
                                                    </div>
                                                    <div class="meta-item" x-show="section.meta_description">
                                                        <label>Meta Description</label>
                                                        <span x-text="section.meta_description"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Meta Data: meta fields only --}}
                                        <template x-if="section.section_type === 'meta'">
                                            <div>
                                                <p class="section-subheading">Meta Data</p>
                                                <div class="blog-meta-grid">
                                                    <div class="meta-item" x-show="section.meta_title">
                                                        <label>Meta Title</label>
                                                        <span x-text="section.meta_title"></span>
                                                    </div>
                                                    <div class="meta-item" x-show="section.meta_description">
                                                        <label>Meta Description</label>
                                                        <span x-text="section.meta_description"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        @if($job->status === \App\Enums\JobStatus::InReview)
                                        <div class="notes-area">
                                            <label>Feedback (required if declining)</label>
                                            <textarea
                                                x-model="section.client_notes"
                                                placeholder="Tell us what needs to change..."
                                                @blur="saveSectionNotes(section)"
                                                @input="section.declineError = false"
                                            ></textarea>
                                        </div>
                                        <div class="review-actions">
                                            <button type="button" class="btn-approve" :class="{ 'selected': section.status === 'approved' }" @click="setSectionStatus(section, 'approved')">Approve</button>
                                            <button type="button" class="btn-decline" :class="{ 'selected': section.status === 'declined' }" @click="setSectionStatus(section, 'declined')">Decline</button>
                                            <span class="decline-error" x-show="section.declineError" x-cloak>Please tell us the reason so we can improve this section</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                </div>{{-- end iteration wrapper --}}
                            </template>
                        </div>
                    </template>

                </div>
            </div>
        </template>
    </main>

    @if($job->status === \App\Enums\JobStatus::InReview)
    <footer class="review-footer" x-show="!completed">
        <div class="review-footer-inner">
            <span style="font-size: 13px; color: var(--text2);" x-text="reviewedCount + ' of ' + totalCount + ' ' + (isCopywriting ? 'sections' : 'articles') + ' reviewed'"></span>
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
