@extends('layouts.admin')

@section('title', 'Edit Article')
@section('page-title', 'Edit Article')

@push('head')
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    @include('admin.blogs._quill-init', ['initialContent' => old('content', $blog->content ?? '')])
@endpush

@section('content')
    <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6">
        <h2 class="text-sm font-semibold text-neutral-900 mb-5">Edit: {{ $blog->title }}</h2>
        <form method="POST" action="{{ route('admin.jobs.blogs.update', [$job, $blog]) }}">
            @csrf
            @method('PUT')
            @include('admin.blogs._form', ['blog' => $blog])
            <div style="display: flex; gap: 8px; margin-top: 8px;">
                <button type="submit" class="btn btn-brand">Save Changes</button>
                <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-muted">Cancel</a>
            </div>
        </form>
    </div>
@endsection
