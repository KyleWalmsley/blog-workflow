@extends('layouts.admin')

@section('title', 'Add Article')
@section('page-title', 'Add Article')

@push('head')
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    @include('admin.blogs._quill-init', ['initialContent' => old('content', '')])
@endpush

@section('content')
    <div class="card" style="max-width: 800px;">
        <h2 class="card-title">Add Article to {{ $job->title }}</h2>
        <form method="POST" action="{{ route('admin.jobs.blogs.store', $job) }}">
            @csrf
            @include('admin.blogs._form')
            <div style="display: flex; gap: 8px; margin-top: 8px;">
                <button type="submit" class="btn btn-brand">Add Article</button>
                <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-muted">Cancel</a>
            </div>
        </form>
    </div>
@endsection
