@php $client = $client ?? null; @endphp

<div class="form-group">
    <label class="form-label" for="name">Name *</label>
    <input type="text" id="name" name="name" class="form-input" value="{{ old('name', $client?->name) }}" required>
</div>

<div class="form-group">
    <label class="form-label" for="logo">Logo</label>
    @if($client?->logo_url)
        <div style="margin-bottom: 8px;">
            <img src="{{ $client->logo_url }}" alt="{{ $client->name }}" style="height: 48px; border-radius: 8px;">
            <label style="display: block; margin-top: 6px; font-size: 12px; color: var(--text2);">
                <input type="checkbox" name="remove_logo" value="1"> Remove current logo
            </label>
        </div>
    @endif
    <input type="file" id="logo" name="logo" class="form-input" accept="image/*">
</div>

<div class="form-group">
    <label class="form-label" for="website">Website</label>
    <input type="url" id="website" name="website" class="form-input" value="{{ old('website', $client?->website) }}" placeholder="https://">
</div>

<div class="form-group">
    <label class="form-label" for="business_description">Business Description</label>
    <textarea id="business_description" name="business_description" class="form-textarea">{{ old('business_description', $client?->business_description) }}</textarea>
</div>

<div class="grid g2">
    <div class="form-group">
        <label class="form-label" for="primary_keywords">Primary Keywords</label>
        <textarea id="primary_keywords" name="primary_keywords" class="form-textarea" rows="3">{{ old('primary_keywords', $client?->primary_keywords) }}</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="secondary_keywords">Secondary Keywords</label>
        <textarea id="secondary_keywords" name="secondary_keywords" class="form-textarea" rows="3">{{ old('secondary_keywords', $client?->secondary_keywords) }}</textarea>
    </div>
</div>

<div class="grid g2">
    <div class="form-group">
        <label class="form-label" for="target_locations">Target Locations</label>
        <input type="text" id="target_locations" name="target_locations" class="form-input" value="{{ old('target_locations', $client?->target_locations) }}">
    </div>
    <div class="form-group">
        <label class="form-label" for="target_audience">Target Audience</label>
        <input type="text" id="target_audience" name="target_audience" class="form-input" value="{{ old('target_audience', $client?->target_audience) }}">
    </div>
</div>

<div class="form-group">
    <label class="form-label" for="tone_of_voice">Tone of Voice</label>
    <input type="text" id="tone_of_voice" name="tone_of_voice" class="form-input" value="{{ old('tone_of_voice', $client?->tone_of_voice) }}">
</div>

<div class="form-group">
    <label class="form-label" for="status">Status</label>
    <select id="status" name="status" class="form-select">
        @foreach(\App\Enums\ClientStatus::cases() as $status)
            <option value="{{ $status->value }}" @selected(old('status', $client?->status?->value ?? 'active') === $status->value)>{{ $status->label() }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label class="form-label" for="notes">Internal Notes</label>
    <textarea id="notes" name="notes" class="form-textarea">{{ old('notes', $client?->notes) }}</textarea>
</div>
