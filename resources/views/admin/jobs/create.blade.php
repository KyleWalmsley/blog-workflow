@extends('layouts.admin')

@section('title', 'New Job')
@section('page-title', 'New Job')

@section('content')
    <div class="card" style="max-width: 560px;">
        <h2 class="card-title">Create Job</h2>
        <form method="POST" action="{{ route('admin.jobs.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="client_id">Client *</label>
                <select id="client_id" name="client_id" class="form-select" required>
                    <option value="">Select client...</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" @selected(old('client_id', request('client_id')) == $client->id)>{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="title">Job Title *</label>
                <input type="text" id="title" name="title" class="form-input" value="{{ old('title') }}" required placeholder="e.g. March 2026 Blog Batch">
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-brand">Create Job</button>
                <a href="{{ route('admin.jobs.index') }}" class="btn btn-muted">Cancel</a>
            </div>
        </form>
    </div>
@endsection
