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
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
            @if($notifications->isEmpty())
                <div class="empty-state"><p>No notifications yet.</p></div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-neutral-100 bg-neutral-50">
                            <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Type</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Message</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Job</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">When</th>
                            <th class="px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($notifications as $notification)
                            <tr class="transition-colors {{ $notification->read_at ? 'hover:bg-neutral-50' : 'bg-blue-50 hover:bg-blue-50/80' }}">
                                <td class="px-6 py-4">@include('admin.partials.status-badge', ['status' => $notification->type])</td>
                                <td class="px-6 py-4 text-neutral-700">{{ $notification->message }}</td>
                                <td class="px-6 py-4 text-neutral-600">
                                    @if($notification->job)
                                        <a href="{{ route('admin.jobs.show', $notification->job) }}" class="text-blue-600 hover:underline">{{ $notification->job->title }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-neutral-500">{{ $notification->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 text-right">
                                    @if(!$notification->read_at)
                                        <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-muted">Mark Read</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-neutral-400">Read</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination px-6 pb-5">{{ $notifications->links() }}</div>
            @endif
        </div>
    @endif

    {{-- Outgoing tab --}}
    @if($activeTab === 'outgoing')
        <div class="bg-white border border-neutral-200 rounded-xl shadow-sm overflow-hidden">
            @if($outgoingEmails->isEmpty())
                <div class="empty-state"><p>No outgoing emails logged yet.</p></div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-neutral-100 bg-neutral-50">
                            <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Type</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Recipient</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Job</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Client</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Status</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-neutral-500 uppercase tracking-wide">Sent</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100">
                        @foreach($outgoingEmails as $email)
                            <tr class="hover:bg-neutral-50 transition-colors">
                                <td class="px-6 py-4 text-neutral-700">{{ $email->type->label() }}</td>
                                <td class="px-6 py-4 font-mono text-xs text-neutral-600">{{ $email->recipient_email }}</td>
                                <td class="px-6 py-4 text-neutral-600">
                                    @if($email->job)
                                        <a href="{{ route('admin.jobs.show', $email->job) }}" class="text-blue-600 hover:underline">{{ $email->job->title }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-neutral-600">{{ $email->client?->name ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if($email->status->value === 'sent')
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-1.5 py-0.5 rounded bg-green-50 text-green-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>{{ $email->status->label() }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-1.5 py-0.5 rounded bg-red-50 text-red-600" title="{{ $email->error_message }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>{{ $email->status->label() }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-neutral-400 whitespace-nowrap">
                                    {{ $email->sent_at?->format('d M Y H:i') ?? $email->created_at->format('d M Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination px-6 pb-5">{{ $outgoingEmails->links() }}</div>
            @endif
        </div>
    @endif
@endsection
