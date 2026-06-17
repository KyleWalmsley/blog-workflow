@extends('layouts.admin')

@section('title', 'Jobs')
@section('page-title', 'Jobs')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="card-title">All Jobs</h2>
            <p class="card-sub">Blog batches and review workflow</p>
        </div>
        <a href="{{ route('admin.jobs.create') }}" class="btn btn-brand">New Job</a>
    </div>

    <div class="card">
        @if($jobs->isEmpty())
            <div class="empty-state"><p>No jobs yet.</p></div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Revisions</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jobs as $job)
                            <tr>
                                <td><a href="{{ route('admin.jobs.show', $job) }}">{{ $job->title }}</a></td>
                                <td>{{ $job->client->name }}</td>
                                <td>@include('admin.partials.status-badge', ['status' => $job->status])</td>
                                <td>{{ $job->revision_count }} / {{ $job->maxRevisions() }}</td>
                                <td>{{ $job->updated_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $jobs->links() }}</div>
        @endif
    </div>
@endsection
