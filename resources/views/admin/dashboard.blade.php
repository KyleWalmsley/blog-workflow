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

    <div class="grid grid-cols-4 gap-5">
        {{-- Active Clients --}}
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-7">
            <div class="mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m0 0a5 5 0 106 0m-6 0a5 5 0 006 0" />
                </svg>
            </div>
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide mb-3">Active Clients</p>
            <p class="text-3xl font-semibold text-neutral-900 font-mono">{{ $stats['active_clients'] }}</p>
        </div>

        {{-- Draft Jobs --}}
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-7">
            <div class="mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-3-3v6M4 6h16M4 10h4M4 14h4M4 18h16" />
                </svg>
            </div>
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide mb-3">Draft Jobs</p>
            <p class="text-3xl font-semibold text-neutral-900 font-mono">{{ $stats['draft_jobs'] }}</p>
        </div>

        {{-- In Review --}}
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-7">
            <div class="mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide mb-3">In Review</p>
            <p class="text-3xl font-semibold text-neutral-900 font-mono">{{ $stats['in_review_jobs'] }}</p>
        </div>

        {{-- Completed --}}
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-7">
            <div class="mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs font-medium text-neutral-500 uppercase tracking-wide mb-3">Completed</p>
            <p class="text-3xl font-semibold text-neutral-900 font-mono">{{ $stats['completed_jobs'] }}</p>
        </div>
    </div>

    <div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-6 border-b border-neutral-100">
            <h2 class="text-xs font-semibold text-neutral-500 uppercase tracking-wide">Recent Jobs</h2>
            <p class="text-sm text-neutral-400 mt-1.5">Latest workflow activity</p>
        </div>

        @if($recentJobs->isEmpty())
            <div class="empty-state">
                <p>No jobs yet. Create your first job to get started.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-100 bg-neutral-50">
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Title</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Client</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach($recentJobs as $job)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-neutral-800">
                                <a href="{{ route('admin.jobs.show', $job) }}" class="text-blue-600 hover:underline">{{ $job->title }}</a>
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-600">{{ $job->client->name }}</td>
                            <td class="px-6 py-4">@include('admin.partials.status-badge', ['status' => $job->status])</td>
                            <td class="px-6 py-4 text-sm text-neutral-500">{{ $job->updated_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
