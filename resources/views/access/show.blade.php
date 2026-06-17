@extends('layouts.guest')

@section('content')
    <h1 class="access-title">Blog Workflow</h1>
    <p class="access-sub">Enter your access code to continue</p>

    <form method="POST" action="{{ route('access.store') }}">
        @csrf
        <div class="form-group">
            <label class="form-label" for="access_code">Access Code</label>
            <input type="password" id="access_code" name="access_code" class="form-input" value="{{ old('access_code') }}" autofocus required>
            @error('access_code')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="btn btn-brand" style="width: 100%; justify-content: center;">Unlock Admin</button>
    </form>
@endsection
