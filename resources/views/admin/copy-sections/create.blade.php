@extends('layouts.admin')

@section('title', 'Add Section')
@section('page-title', 'Add Section')

@push('head')
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    @include('admin.blogs._quill-init', ['initialContent' => old('content', '')])
@endpush

@section('content')
    <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6">
        <h2 class="text-sm font-semibold text-neutral-900 mb-5">Add Section to {{ $job->title }}</h2>
        <form method="POST" action="{{ route('admin.jobs.copy-sections.store', $job) }}">
            @csrf
            @include('admin.copy-sections._form')
            <div style="display: flex; gap: 8px; margin-top: 8px;">
                <button type="submit" class="btn btn-brand">Add Section</button>
                <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-muted">Cancel</a>
            </div>
        </form>
    </div>
@endsection
