<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $section->section_type->label() }}{{ $section->title ? ' — ' . $section->title : '' }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.6; color: #1a1a2e; margin: 40px; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        .section-type { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 20px; }
        .meta { font-size: 10px; color: #555; margin-bottom: 20px; border-bottom: 1px solid #e0e0e0; padding-bottom: 12px; }
        .meta p { margin: 4px 0; }
        .label { font-weight: bold; color: #333; }
        .banner-headline { font-size: 22px; font-weight: bold; margin: 16px 0 8px; }
        .banner-sub { font-size: 15px; color: #555; margin-bottom: 16px; }
        h2, h3 { margin-top: 20px; }
        p { margin-bottom: 12px; }
        ul, ol { margin-bottom: 12px; padding-left: 20px; }
        li { margin-bottom: 4px; }
    </style>
</head>
<body>
    <p class="section-type">{{ $section->section_type->label() }}</p>
    <h1>{{ $section->title ?: $section->headline ?: $section->section_type->label() }}</h1>

    <div class="meta">
        <p><span class="label">Client:</span> {{ $client->name }}</p>
        <p><span class="label">Job:</span> {{ $job->title }}</p>
    </div>

    @if($section->section_type === \App\Enums\CopySectionType::Banner)
        @if($section->headline)
            <p class="banner-headline">{{ $section->headline }}</p>
        @endif
        @if($section->sub_headline)
            <p class="banner-sub">{{ $section->sub_headline }}</p>
        @endif
    @endif

    @if($section->content)
        <div class="content">
            {!! strip_tags($section->content, '<p><br><h1><h2><h3><h4><ul><ol><li><strong><em><a>') !!}
        </div>
    @endif

    @if($section->meta_title || $section->meta_description)
        <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 20px 0;">
        @if($section->meta_title)
            <p><span class="label">Meta Title:</span> {{ $section->meta_title }}</p>
        @endif
        @if($section->meta_description)
            <p><span class="label">Meta Description:</span> {{ $section->meta_description }}</p>
        @endif
    @endif
</body>
</html>
