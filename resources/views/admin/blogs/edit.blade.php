@extends('layouts.admin')

@section('title', 'Edit Article')
@section('page-title', 'Edit Article')

@section('content')
    <div class="card" style="max-width: 800px;">
        <h2 class="card-title">Edit: {{ $blog->title }}</h2>
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
