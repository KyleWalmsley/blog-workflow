@extends('layouts.admin')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="card-title">Notification Log</h2>
            <p class="card-sub">Internal workflow alerts</p>
        </div>
        @if($unreadNotifications > 0)
            <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-muted">Mark All Read</button>
            </form>
        @endif
    </div>

    <div class="card">
        @if($notifications->isEmpty())
            <div class="empty-state"><p>No notifications yet.</p></div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Message</th>
                            <th>Job</th>
                            <th>When</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $notification)
                            <tr style="{{ $notification->read_at ? '' : 'background: var(--brandglow);' }}">
                                <td>@include('admin.partials.status-badge', ['status' => $notification->type])</td>
                                <td>{{ $notification->message }}</td>
                                <td>
                                    @if($notification->job)
                                        <a href="{{ route('admin.jobs.show', $notification->job) }}">{{ $notification->job->title }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $notification->created_at->diffForHumans() }}</td>
                                <td>
                                    @if(!$notification->read_at)
                                        <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-muted">Mark Read</button>
                                        </form>
                                    @else
                                        <span style="font-size: 11px; color: var(--text3);">Read</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $notifications->links() }}</div>
        @endif
    </div>
@endsection
