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

    <div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
        @if($clients->isEmpty())
            <div class="empty-state">
                <p>No clients yet. Add your first client to begin.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-neutral-100 bg-neutral-50">
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Name</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Jobs</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Website</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @foreach($clients as $client)
                        <tr class="hover:bg-neutral-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-neutral-800">
                                <a href="{{ route('admin.clients.show', $client) }}" class="text-blue-600 hover:underline">{{ $client->name }}</a>
                            </td>
                            <td class="px-6 py-4">@include('admin.partials.status-badge', ['status' => $client->status])</td>
                            <td class="px-6 py-4 text-neutral-600">{{ $client->jobs_count }}</td>
                            <td class="px-6 py-4 text-neutral-500 text-xs">{{ $client->website ? parse_url($client->website, PHP_URL_HOST) : '—' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-sm btn-muted">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination px-6 pb-5">{{ $clients->links() }}</div>
        @endif
    </div>
@endsection
