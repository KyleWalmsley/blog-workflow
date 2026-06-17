@extends('layouts.review')

@section('content')
    <header class="review-header">
        <div class="review-header-inner">
            <div class="client-logo-placeholder">{{ strtoupper(substr($client->name, 0, 1)) }}</div>
            <div>
                <h1 class="review-title">{{ $job->title }}</h1>
                <p class="review-subtitle">{{ $client->name }}</p>
            </div>
        </div>
    </header>
    <main class="review-main">
        <div class="flash flash-error">
            This review link is not currently active. Please contact your account manager.
        </div>
    </main>
@endsection
