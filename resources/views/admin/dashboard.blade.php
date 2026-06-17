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

    <div class="grid g4">
        <div class="card">
            <div class="kpi-label">Active Clients</div>
            <div class="kpi-val">{{ $stats['active_clients'] }}</div>
        </div>
        <div class="card">
            <div class="kpi-label">Draft Jobs</div>
            <div class="kpi-val">{{ $stats['draft_jobs'] }}</div>
        </div>
        <div class="card">
            <div class="kpi-label">In Review</div>
            <div class="kpi-val">{{ $stats['in_review_jobs'] }}</div>
        </div>
        <div class="card">
            <div class="kpi-label">Completed</div>
            <div class="kpi-val">{{ $stats['completed_jobs'] }}</div>
        </div>
    </div>

    <div class="card">
        <div class="page-header">
            <div>
                <h2 class="card-title">Recent Jobs</h2>
                <p class="card-sub">Latest workflow activity</p>
            </div>
            <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary">New Job</a>
        </div>

        @if($recentJobs->isEmpty())
            <div class="empty-state">
                <p>No jobs yet. Create your first job to get started.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentJobs as $job)
                            <tr>
                                <td><a href="{{ route('admin.jobs.show', $job) }}">{{ $job->title }}</a></td>
                                <td>{{ $job->client->name }}</td>
                                <td>@include('admin.partials.status-badge', ['status' => $job->status])</td>
                                <td>{{ $job->updated_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
