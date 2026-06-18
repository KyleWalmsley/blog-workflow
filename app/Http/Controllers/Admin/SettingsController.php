<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SmtpSettingsRequest;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Services\EmailService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        private NotificationService $notificationService,
        private EmailService $emailService,
    ) {}

    public function index(): View
    {
        $smtpKeys = ['smtp_host', 'smtp_port', 'smtp_encryption', 'smtp_username', 'smtp_password', 'from_name', 'from_email', 'reply_to_email'];

        return view('admin.settings.index', [
            'smtp' => Setting::getMany($smtpKeys),
            'templates' => EmailTemplate::all(),
            'activeTab' => request('tab', 'smtp'),
            'unreadNotifications' => $this->notificationService->unreadCount(),
        ]);
    }

    public function updateSmtp(SmtpSettingsRequest $request): RedirectResponse
    {
        foreach ($request->validated() as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings.index', ['tab' => 'smtp'])
            ->with('success', 'SMTP settings saved.');
    }

    public function testEmail(Request $request): RedirectResponse
    {
        $request->validate(['test_recipient' => ['required', 'email']]);

        try {
            $this->emailService->sendTestEmail($request->test_recipient);
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.index', ['tab' => 'smtp'])
                ->with('error', 'Test email failed: '.$e->getMessage());
        }

        return redirect()->route('admin.settings.index', ['tab' => 'smtp'])
            ->with('success', 'Test email sent to '.$request->test_recipient.'.');
    }

    public function updateTemplate(Request $request, EmailTemplate $template): RedirectResponse
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
        ]);

        $template->update($request->only('subject', 'body'));

        return redirect()->route('admin.settings.index', ['tab' => 'templates'])
            ->with('success', '"'.$template->label.'" template saved.');
    }
}
