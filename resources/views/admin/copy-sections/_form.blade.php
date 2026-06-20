@php $section = $section ?? null; $selectedType = old('section_type', $section?->section_type?->value ?? ''); @endphp

<div x-data="{ sectionType: '{{ $selectedType }}' }">

    <div class="form-group">
        <label class="form-label" for="section_type">Section Type *</label>
        <select id="section_type" name="section_type" class="form-select" required x-model="sectionType">
            <option value="">Select type...</option>
            @foreach($sectionTypes as $type)
                <option value="{{ $type->value }}">{{ $type->label() }}</option>
            @endforeach
        </select>
    </div>

    {{-- Headline field: banner uses it as "Headline", about_us as "Heading" --}}
    <div x-show="sectionType === 'banner' || sectionType === 'about_us'" x-cloak>
        <div class="form-group">
            <label class="form-label" for="headline" x-text="sectionType === 'about_us' ? 'Heading' : 'Headline'">Headline</label>
            <input type="text" id="headline" name="headline" class="form-input" value="{{ old('headline', $section?->headline) }}"
                :placeholder="sectionType === 'about_us' ? 'e.g. About Blossom & Bloom' : 'e.g. Helping Businesses Grow Online'">
        </div>
        {{-- Sub-headline: banner only --}}
        <div class="form-group" x-show="sectionType === 'banner'" x-cloak>
            <label class="form-label" for="sub_headline">Sub-Headline</label>
            <input type="text" id="sub_headline" name="sub_headline" class="form-input" value="{{ old('sub_headline', $section?->sub_headline) }}" placeholder="e.g. Expert digital marketing solutions for ambitious brands">
        </div>
    </div>

    {{-- Service name / Meta page name --}}
    <div x-show="sectionType === 'service' || sectionType === 'meta'" x-cloak class="form-group">
        <label class="form-label" for="title" x-text="sectionType === 'meta' ? 'Page' : 'Service / Product Name'">Name</label>
        <input type="text" id="title" name="title" class="form-input" value="{{ old('title', $section?->title) }}"
            :placeholder="sectionType === 'meta' ? 'e.g. Homepage, News Page, Contact Page' : 'e.g. SEO Consulting'">
    </div>

    {{-- Rich content: About Us, About Page, Service --}}
    <div x-show="sectionType === 'about_us' || sectionType === 'about_page' || sectionType === 'service'" x-cloak class="form-group">
        <label class="form-label">Content</label>
        <div id="quill-editor" style="min-height: 340px; background: var(--bg1); border: 1px solid var(--border); border-radius: 8px; font-size: 14px;"></div>
        <textarea id="content" name="content" style="display:none;">{{ old('content', $section?->content) }}</textarea>
    </div>

    {{-- Meta fields: About Page, Service, Meta --}}
    <div x-show="sectionType === 'about_page' || sectionType === 'service' || sectionType === 'meta'" x-cloak>
        <div class="form-group">
            <label class="form-label" for="meta_title">Meta Title</label>
            <input type="text" id="meta_title" name="meta_title" class="form-input" value="{{ old('meta_title', $section?->meta_title) }}">
        </div>
        <div class="form-group">
            <label class="form-label" for="meta_description">Meta Description</label>
            <textarea id="meta_description" name="meta_description" class="form-textarea" rows="3">{{ old('meta_description', $section?->meta_description) }}</textarea>
        </div>
    </div>

</div>
