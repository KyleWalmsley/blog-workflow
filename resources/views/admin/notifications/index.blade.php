@extends('layouts.admin')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
    <div class="page-heading">
        <h1>Notifications</h1>
        @if($activeTab === 'incoming' && $unreadNotifications > 0)
            <div class="page-actions">
                <form method="POST" action="{{ route('admin.notifications.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-secondary">Mark All Read</button>
                </form>
            </div>
        @endif
    </div>

    {{-- Tab navigation --}}
    <div class="settings-tabs">
        <a href="{{ route('admin.notifications.index', ['tab' => 'incoming']) }}"
           class="settings-tab {{ $activeTab === 'incoming' ? 'active' : '' }}">
            Incoming
            @if($unreadNotifications > 0)
                <span style="background: var(--rose); color: #fff; font-size: 10px; font-weight: 600; padding: 1px 6px; border-radius: 99px; margin-left: 6px;">{{ $unreadNotifications }}</span>
            @endif
        </a>
        <a href="{{ route('admin.notifications.index', ['tab' => 'outgoing']) }}"
           class="settings-tab {{ $activeTab === 'outgoing' ? 'active' : '' }}">
            Outgoing
        </a>
    </div>
    <div class="settings-tab-divider"></div>

    {{-- Incoming tab --}}
    @if($activeTab === 'incoming')
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
    @endif

    {{-- Outgoing tab --}}
    @if($activeTab === 'outgoing')
        <div class="card">
            @if($outgoingEmails->isEmpty())
                <div class="empty-state"><p>No outgoing emails logged yet.</p></div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Recipient</th>
                                <th>Job</th>
                                <th>Client</th>
                                <th>Status</th>
                                <th>Sent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($outgoingEmails as $email)
                                <tr>
                                    <td>{{ $email->type->label() }}</td>
                                    <td style="font-family: monospace; font-size: 12px;">{{ $email->recipient_email }}</td>
                                    <td>
                                        @if($email->job)
                                            <a href="{{ route('admin.jobs.show', $email->job) }}">{{ $email->job->title }}</a>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $email->client?->name ?? '—' }}</td>
                                    <td>
                                        @if($email->status->value === 'sent')
                                            <span class="badge badge-green"><span class="badge-dot"></span>{{ $email->status->label() }}</span>
                                        @else
                                            <span class="badge badge-rose" title="{{ $email->error_message }}"><span class="badge-dot"></span>{{ $email->status->label() }}</span>
                                        @endif
                                    </td>
                                    <td style="white-space: nowrap; font-size: 12px; color: var(--text2);">
                                        {{ $email->sent_at?->format('d M Y H:i') ?? $email->created_at->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination">{{ $outgoingEmails->links() }}</div>
            @endif
        </div>
    @endif
@endsection
