@extends('layouts.admin')

@section('title', 'Jobs')
@section('page-title', 'Jobs')

@section('content')
    <div class="page-heading">
        <h1>Jobs</h1>
        <div class="page-actions">
            <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary">New Job</a>
        </div>
    </div>

    <div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
        @if($jobs->isEmpty())
            <div class="empty-state"><p>No jobs yet.</p></div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-100 bg-neutral-50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Title</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Client</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Status</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Revisions</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach($jobs as $job)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-5 py-3 font-medium">
                                <a href="{{ route('admin.jobs.show', $job) }}" class="text-blue-600 hover:underline">{{ $job->title }}</a>
                            </td>
                            <td class="px-5 py-3 text-neutral-600">{{ $job->client->name }}</td>
                            <td class="px-5 py-3">@include('admin.partials.status-badge', ['status' => $job->status])</td>
                            <td class="px-5 py-3 text-neutral-600 font-mono text-xs">{{ $job->revision_count }} / {{ $job->maxRevisions() }}</td>
                            <td class="px-5 py-3 text-neutral-500">{{ $job->updated_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination px-5 pb-4">{{ $jobs->links() }}</div>
        @endif
    </div>
@endsection
