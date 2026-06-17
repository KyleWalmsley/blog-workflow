<div class="blog-card">
    <div class="blog-card-header" @click="blogs[{{ $index }}].open = !blogs[{{ $index }}].open">
        <div>
            <div class="blog-card-title">{{ $blog->title }}</div>
            <div style="display:flex;gap:6px;margin-top:6px;flex-wrap:wrap;">
                @if($blog->focus_keyword)
                    <span class="chip">{{ $blog->focus_keyword }}</span>
                @endif
                @if($blog->focus_location)
                    <span class="chip">{{ $blog->focus_location }}</span>
                @endif
            </div>
        </div>
        <span class="status-pill" :class="'status-' + blogs[{{ $index }}].status" x-text="blogs[{{ $index }}].status"></span>
    </div>

    <div class="blog-card-body" x-show="blogs[{{ $index }}].open">
        @if($blog->meta_title || $blog->meta_description)
            <div class="meta-grid">
                @if($blog->meta_title)
                    <div><div class="meta-label">Meta Title</div>{{ $blog->meta_title }}</div>
                @endif
                @if($blog->meta_description)
                    <div><div class="meta-label">Meta Description</div>{{ $blog->meta_description }}</div>
                @endif
            </div>
        @endif

        <div class="article-content" @contextmenu.prevent>
            {!! $blog->content !!}
        </div>

        @if($job->status === \App\Enums\JobStatus::InReview)
            <div class="review-actions">
                <button type="button" class="btn-approve" :class="{ active: blogs[{{ $index }}].status === 'approved' }" @click="setStatus(blogs[{{ $index }}], 'approved')">Approve</button>
                <button type="button" class="btn-decline" :class="{ active: blogs[{{ $index }}].status === 'declined' }" @click="setStatus(blogs[{{ $index }}], 'declined')">Decline</button>
            </div>

            <textarea
                x-show="blogs[{{ $index }}].status === 'declined'"
                class="notes-area"
                x-model="blogs[{{ $index }}].client_notes"
                @blur="saveNotes(blogs[{{ $index }}])"
                placeholder="Please explain what needs to be changed..."
            ></textarea>
        @endif
    </div>
</div>
