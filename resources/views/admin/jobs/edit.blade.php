@extends('layouts.admin')

@section('title', 'Edit ' . $job->title)
@section('page-title', 'Edit Job')

@section('content')
    <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6">
        <h2 class="text-sm font-semibold text-neutral-900 mb-5">Edit Job</h2>
        <form method="POST" action="{{ route('admin.jobs.update', $job) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label" for="job_type">Job Type *</label>
                <select id="job_type" name="job_type" class="form-select" required>
                    @foreach($jobTypes as $type)
                        <option value="{{ $type->value }}" @selected(old('job_type', $job->job_type->value) === $type->value)>{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="client_id">Client *</label>
                <select id="client_id" name="client_id" class="form-select" required>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" @selected(old('client_id', $job->client_id) == $client->id)>{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="title">Job Title *</label>
                <input type="text" id="title" name="title" class="form-input" value="{{ old('title', $job->title) }}" required>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-brand">Save Changes</button>
                <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-muted">Cancel</a>
            </div>
        </form>
    </div>
@endsection
