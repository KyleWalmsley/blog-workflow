@extends('layouts.admin')

@section('title', 'New Client')
@section('page-title', 'New Client')

@section('content')
    <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6" style="max-width: 720px;">
        <h2 class="text-sm font-semibold text-neutral-900 mb-0.5">Create Client</h2>
        <p class="text-xs text-neutral-400 mb-5">Add a new client profile</p>

        <form method="POST" action="{{ route('admin.clients.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.clients._form')
            <div style="display: flex; gap: 8px; margin-top: 8px;">
                <button type="submit" class="btn btn-brand">Create Client</button>
                <a href="{{ route('admin.clients.index') }}" class="btn btn-muted">Cancel</a>
            </div>
        </form>
    </div>
@endsection
