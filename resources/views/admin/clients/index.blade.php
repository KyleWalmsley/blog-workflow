@extends('layouts.admin')

@section('title', 'Clients')
@section('page-title', 'Clients')

@section('content')
    <div class="page-heading">
        <h1>Clients</h1>
        <div class="page-actions">
            <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">Add Client</a>
        </div>
    </div>

    <div class="card">
        @if($clients->isEmpty())
            <div class="empty-state">
                <p>No clients yet. Add your first client to begin.</p>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Jobs</th>
                            <th>Website</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                            <tr>
                                <td><a href="{{ route('admin.clients.show', $client) }}">{{ $client->name }}</a></td>
                                <td>@include('admin.partials.status-badge', ['status' => $client->status])</td>
                                <td>{{ $client->jobs_count }}</td>
                                <td>{{ $client->website ? parse_url($client->website, PHP_URL_HOST) : '—' }}</td>
                                <td><a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-sm btn-muted">Edit</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $clients->links() }}</div>
        @endif
    </div>
@endsection
