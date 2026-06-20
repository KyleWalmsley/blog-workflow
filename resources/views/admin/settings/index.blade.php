@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
    <div class="page-heading">
        <h1>Settings</h1>
    </div>

    {{-- Tab navigation --}}
    <div class="settings-tab-nav">
        <div class="settings-tabs">
            <a href="{{ route('admin.settings.index', ['tab' => 'smtp']) }}"
               class="settings-tab {{ $activeTab === 'smtp' ? 'active' : '' }}">
                SMTP Settings
            </a>
            <a href="{{ route('admin.settings.index', ['tab' => 'templates']) }}"
               class="settings-tab {{ $activeTab === 'templates' ? 'active' : '' }}">
                Email Templates
            </a>
        </div>
        <div class="settings-tab-divider"></div>
    </div>

    {{-- SMTP Settings Tab --}}
    @if($activeTab === 'smtp')
        <div class="flex flex-col gap-5">
            <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6">
                <h2 class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-0.5">SMTP Settings</h2>
                <p class="text-xs text-neutral-400 mb-5">Used to send review invitation emails to clients. Settings are stored in the database.</p>

                <form method="POST" action="{{ route('admin.settings.smtp.update') }}">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label" for="smtp_host">SMTP Host</label>
                            <input type="text" id="smtp_host" name="smtp_host" class="form-input"
                                   value="{{ old('smtp_host', $smtp['smtp_host']) }}"
                                   placeholder="smtp.example.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="smtp_port">SMTP Port</label>
                            <input type="number" id="smtp_port" name="smtp_port" class="form-input"
                                   value="{{ old('smtp_port', $smtp['smtp_port'] ?? 587) }}"
                                   placeholder="587">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="smtp_encryption">Encryption</label>
                        <select id="smtp_encryption" name="smtp_encryption" class="form-select">
                            @foreach(['tls' => 'TLS (Recommended)', 'ssl' => 'SSL', 'none' => 'None'] as $val => $label)
                                <option value="{{ $val }}" @selected(old('smtp_encryption', $smtp['smtp_encryption'] ?? 'tls') === $val)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label" for="smtp_username">SMTP Username</label>
                            <input type="text" id="smtp_username" name="smtp_username" class="form-input"
                                   value="{{ old('smtp_username', $smtp['smtp_username']) }}"
                                   autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="smtp_password">SMTP Password</label>
                            <input type="password" id="smtp_password" name="smtp_password" class="form-input"
                                   value="{{ old('smtp_password', $smtp['smtp_password']) }}"
                                   autocomplete="new-password">
                        </div>
                    </div>

                    <div class="settings-section-divider"></div>

                    <div class="form-group">
                        <label class="form-label" for="from_name">From Name</label>
                        <input type="text" id="from_name" name="from_name" class="form-input"
                               value="{{ old('from_name', $smtp['from_name']) }}"
                               placeholder="Product Delivery @ Your Business">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label" for="from_email">From Email</label>
                            <input type="email" id="from_email" name="from_email" class="form-input"
                                   value="{{ old('from_email', $smtp['from_email']) }}"
                                   placeholder="delivery@yourbusiness.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="reply_to_email">Reply To Email</label>
                            <input type="email" id="reply_to_email" name="reply_to_email" class="form-input"
                                   value="{{ old('reply_to_email', $smtp['reply_to_email']) }}"
                                   placeholder="hello@yourbusiness.com">
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save SMTP Settings</button>
                    </div>
                </form>
            </div>

            <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6">
                <h2 class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-0.5">Send Test Email</h2>
                <p class="text-xs text-neutral-400 mb-5">Verify your SMTP settings are working by sending a test email.</p>

                <form method="POST" action="{{ route('admin.settings.test-email') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="test_recipient">Recipient Email</label>
                        <input type="email" id="test_recipient" name="test_recipient" class="form-input"
                               value="{{ old('test_recipient') }}"
                               placeholder="you@example.com" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-secondary">Send Test Email</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Email Templates Tab --}}
    @if($activeTab === 'templates')
        <div class="flex flex-col gap-5">
            @forelse($templates as $template)
                <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6">
                    <h2 class="text-xs font-semibold text-neutral-500 uppercase tracking-wide mb-0.5">{{ $template->label }}</h2>
                    <p class="text-xs text-neutral-400 mb-5">
                        Template name: <code style="background: var(--bg3); padding: 1px 6px; border-radius: 4px; font-size: 12px;">{{ $template->name }}</code>
                    </p>

                    <form method="POST" action="{{ route('admin.settings.templates.update', $template) }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="subject_{{ $template->id }}">Subject</label>
                            <input type="text" id="subject_{{ $template->id }}" name="subject" class="form-input"
                                   value="{{ old('subject', $template->subject) }}" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="body_{{ $template->id }}">Email Body</label>
                            <p class="text-xs text-neutral-400 mb-1.5">The main message paragraph shown in the email. Leave blank to use the default copy.</p>
                            <textarea id="body_{{ $template->id }}" name="body" class="form-textarea" rows="10" required>{{ old('body', $template->body) }}</textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Save Template</button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="bg-white border border-neutral-200 rounded-xl shadow-sm p-6">
                    <div class="empty-state"><p>No email templates found. Run <code>php artisan db:seed</code> to create the defaults.</p></div>
                </div>
            @endforelse
        </div>
    @endif
@endsection
