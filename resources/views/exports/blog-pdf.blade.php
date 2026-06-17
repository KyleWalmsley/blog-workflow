<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $blog->title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.6; color: #1a2e1f; margin: 40px; }
        h1 { font-size: 22px; margin-bottom: 8px; }
        .meta { font-size: 10px; color: #666; margin-bottom: 24px; border-bottom: 1px solid #ddd; padding-bottom: 12px; }
        .meta p { margin: 4px 0; }
        h2, h3 { margin-top: 20px; }
        p { margin-bottom: 12px; }
    </style>
</head>
<body>
    <h1>{{ $blog->title }}</h1>
    <div class="meta">
        <p><strong>Client:</strong> {{ $client->name }}</p>
        <p><strong>Job:</strong> {{ $job->title }}</p>
        @if($blog->meta_title)
            <p><strong>Meta Title:</strong> {{ $blog->meta_title }}</p>
        @endif
        @if($blog->meta_description)
            <p><strong>Meta Description:</strong> {{ $blog->meta_description }}</p>
        @endif
        @if($blog->focus_keyword)
            <p><strong>Focus Keyword:</strong> {{ $blog->focus_keyword }}</p>
        @endif
        @if($blog->focus_location)
            <p><strong>Focus Location:</strong> {{ $blog->focus_location }}</p>
        @endif
    </div>
    <div class="content">
        {!! strip_tags($blog->content, '<p><br><h1><h2><h3><h4><ul><ol><li><strong><em><a>') !!}
    </div>
</body>
</html>
