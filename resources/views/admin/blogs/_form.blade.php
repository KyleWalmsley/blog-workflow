@php $blog = $blog ?? null; @endphp

<div class="form-group">
    <label class="form-label" for="title">Title *</label>
    <input type="text" id="title" name="title" class="form-input" value="{{ old('title', $blog?->title) }}" required>
</div>

<div class="grid g2">
    <div class="form-group">
        <label class="form-label" for="meta_title">Meta Title</label>
        <input type="text" id="meta_title" name="meta_title" class="form-input" value="{{ old('meta_title', $blog?->meta_title) }}">
    </div>
    <div class="form-group">
        <label class="form-label" for="sort_order">Sort Order</label>
        <input type="number" id="sort_order" name="sort_order" class="form-input" value="{{ old('sort_order', $blog?->sort_order ?? 0) }}" min="0">
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="meta_description">Meta Description</label>
    <textarea id="meta_description" name="meta_description" class="form-textarea" rows="2">{{ old('meta_description', $blog?->meta_description) }}</textarea>
</div>

<div class="grid g2">
    <div class="form-group">
        <label class="form-label" for="focus_keyword">Focus Keyword</label>
        <input type="text" id="focus_keyword" name="focus_keyword" class="form-input" value="{{ old('focus_keyword', $blog?->focus_keyword) }}">
    </div>
    <div class="form-group">
        <label class="form-label" for="focus_location">Focus Location</label>
        <input type="text" id="focus_location" name="focus_location" class="form-input" value="{{ old('focus_location', $blog?->focus_location) }}">
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="content">Content (HTML) *</label>
    <textarea id="content" name="content" class="form-textarea" rows="16" required style="font-family: 'DM Mono', monospace; font-size: 12px;">{{ old('content', $blog?->content) }}</textarea>
</div>
