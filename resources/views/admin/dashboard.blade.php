@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <div class="page-heading">
        <h1>Dashboard</h1>
        <div class="page-actions">
            <a href="{{ route('admin.clients.create') }}" class="btn btn-secondary">Add Client</a>
            <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary">Add Job</a>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide mb-1">Active Clients</p>
            <p class="text-2xl font-semibold text-neutral-900 font-mono">{{ $stats['active_clients'] }}</p>
        </div>
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide mb-1">Draft Jobs</p>
            <p class="text-2xl font-semibold text-neutral-900 font-mono">{{ $stats['draft_jobs'] }}</p>
        </div>
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide mb-1">In Review</p>
            <p class="text-2xl font-semibold text-neutral-900 font-mono">{{ $stats['in_review_jobs'] }}</p>
        </div>
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-5">
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide mb-1">Completed</p>
            <p class="text-2xl font-semibold text-neutral-900 font-mono">{{ $stats['completed_jobs'] }}</p>
        </div>
    </div>

    <div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-neutral-100">
            <h2 class="text-sm font-semibold text-neutral-900">Recent Jobs</h2>
            <p class="text-xs text-neutral-400 mt-0.5">Latest workflow activity</p>
        </div>

        @if($recentJobs->isEmpty())
            <div class="empty-state">
                <p>No jobs yet. Create your first job to get started.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-100 bg-neutral-50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Title</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Client</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach($recentJobs as $job)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-5 py-3 text-sm font-medium text-neutral-800">
                                <a href="{{ route('admin.jobs.show', $job) }}" class="text-blue-600 hover:underline">{{ $job->title }}</a>
                            </td>
                            <td class="px-5 py-3 text-sm text-neutral-600">{{ $job->client->name }}</td>
                            <td class="px-5 py-3">@include('admin.partials.status-badge', ['status' => $job->status])</td>
                            <td class="px-5 py-3 text-sm text-neutral-500">{{ $job->updated_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
