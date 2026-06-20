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
                <h2 class="text-base font-semibold text-neutral-900">{{ $client->name }}</h2>
                <p class="mt-1">@include('admin.partials.status-badge', ['status' => $client->status])</p>
            </div>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-muted">Edit</a>
            <a href="{{ route('admin.jobs.create') }}?client_id={{ $client->id }}" class="btn btn-brand">New Job</a>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-5">
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-5">
            <h3 class="text-sm font-semibold text-neutral-900 mb-3">Profile</h3>
            <dl class="text-sm space-y-2">
                @if($client->website)
                    <dt class="text-xs text-neutral-400 mt-2">Website</dt>
                    <dd><a href="{{ $client->website }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline">{{ $client->website }}</a></dd>
                @endif
                @if($client->business_description)
                    <dt class="text-xs text-neutral-400 mt-2">Description</dt>
                    <dd class="text-neutral-600">{{ $client->business_description }}</dd>
                @endif
                @if($client->tone_of_voice)
                    <dt class="text-xs text-neutral-400 mt-2">Tone of Voice</dt>
                    <dd class="text-neutral-600">{{ $client->tone_of_voice }}</dd>
                @endif
            </dl>
        </div>
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-5">
            <h3 class="text-sm font-semibold text-neutral-900 mb-3">SEO Context</h3>
            <dl class="text-sm space-y-2">
                @foreach(['primary_keywords' => 'Primary Keywords', 'secondary_keywords' => 'Secondary Keywords', 'target_locations' => 'Locations', 'target_audience' => 'Audience'] as $field => $label)
                    @if($client->$field)
                        <dt class="text-xs text-neutral-400 mt-2">{{ $label }}</dt>
                        <dd class="text-neutral-600">{{ $client->$field }}</dd>
                    @endif
                @endforeach
            </dl>
        </div>
    </div>

    <div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-neutral-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-neutral-900">Recent Jobs</h3>
        </div>
        @if($client->jobs->isEmpty())
            <div class="empty-state"><p>No jobs for this client yet.</p></div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-100 bg-neutral-50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Title</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Blogs</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach($client->jobs as $job)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-5 py-3 font-medium">
                                <a href="{{ route('admin.jobs.show', $job) }}" class="text-blue-600 hover:underline">{{ $job->title }}</a>
                            </td>
                            <td class="px-5 py-3">@include('admin.partials.status-badge', ['status' => $job->status])</td>
                            <td class="px-5 py-3 text-neutral-600">{{ $job->blogs()->count() }}</td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-sm btn-muted">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
