@extends('layouts.admin')

@section('title', 'Edit ' . $client->name)
@section('page-title', 'Edit Client')

@section('content')
    <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6" style="max-width: 720px;">
        <h2 class="text-sm font-semibold text-neutral-900 mb-5">Edit {{ $client->name }}</h2>

        <form method="POST" action="{{ route('admin.clients.update', $client) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.clients._form', ['client' => $client])
            <div style="display: flex; gap: 8px; margin-top: 8px;">
                <button type="submit" class="btn btn-brand">Save Changes</button>
                <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-muted">Cancel</a>
            </div>
        </form>
    </div>
@endsection
