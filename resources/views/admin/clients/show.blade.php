@extends('layouts.admin')

@section('title', $client->name)
@section('page-title', $client->name)

@section('content')
    <div class="page-header">
        <div style="display: flex; align-items: center; gap: 16px;">
            @if($client->logo_url)
                <img src="{{ $client->logo_url }}" alt="{{ $client->name }}" style="height: 56px; border-radius: 10px;">
            @endif
            <div>
                <h2 class="card-title">{{ $client->name }}</h2>
                <p class="card-sub">@include('admin.partials.status-badge', ['status' => $client->status])</p>
            </div>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-muted">Edit</a>
            <a href="{{ route('admin.jobs.create') }}?client_id={{ $client->id }}" class="btn btn-brand">New Job</a>
        </div>
    </div>

    <div class="grid g2">
        <div class="card">
            <h3 class="card-title">Profile</h3>
            <dl style="margin-top: 12px; font-size: 13px;">
                @if($client->website)
                    <dt style="color: var(--text3); font-size: 11px; margin-top: 10px;">Website</dt>
                    <dd><a href="{{ $client->website }}" target="_blank" rel="noopener">{{ $client->website }}</a></dd>
                @endif
                @if($client->business_description)
                    <dt style="color: var(--text3); font-size: 11px; margin-top: 10px;">Description</dt>
                    <dd style="color: var(--text2);">{{ $client->business_description }}</dd>
                @endif
                @if($client->tone_of_voice)
                    <dt style="color: var(--text3); font-size: 11px; margin-top: 10px;">Tone of Voice</dt>
                    <dd style="color: var(--text2);">{{ $client->tone_of_voice }}</dd>
                @endif
            </dl>
        </div>
        <div class="card">
            <h3 class="card-title">SEO Context</h3>
            <dl style="margin-top: 12px; font-size: 13px; color: var(--text2);">
                @foreach(['primary_keywords' => 'Primary Keywords', 'secondary_keywords' => 'Secondary Keywords', 'target_locations' => 'Locations', 'target_audience' => 'Audience'] as $field => $label)
                    @if($client->$field)
                        <dt style="color: var(--text3); font-size: 11px; margin-top: 10px;">{{ $label }}</dt>
                        <dd>{{ $client->$field }}</dd>
                    @endif
                @endforeach
            </dl>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">Recent Jobs</h3>
        @if($client->jobs->isEmpty())
            <div class="empty-state"><p>No jobs for this client yet.</p></div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Title</th><th>Status</th><th>Blogs</th><th></th></tr>
                    </thead>
                    <tbody>
                        @foreach($client->jobs as $job)
                            <tr>
                                <td><a href="{{ route('admin.jobs.show', $job) }}">{{ $job->title }}</a></td>
                                <td>@include('admin.partials.status-badge', ['status' => $job->status])</td>
                                <td>{{ $job->blogs()->count() }}</td>
                                <td><a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-sm btn-muted">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
