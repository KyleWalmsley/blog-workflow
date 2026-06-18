<?php

namespace App\Services;

use App\Enums\OutgoingEmailStatus;
use App\Enums\OutgoingEmailType;
use App\Mail\ReviewInvitation;
use App\Models\Job;
use App\Models\OutgoingEmail;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Throwable;

class EmailService
{
    public function sendReviewInvitation(Job $job): OutgoingEmail
    {
        $this->applySmtpConfig();

        $mailable = new ReviewInvitation($job);
        $recipient = $job->client->email;

        try {
            Mail::mailer('smtp')->to($recipient)->send($mailable);

            return OutgoingEmail::create([
                'job_id' => $job->id,
                'client_id' => $job->client_id,
                'recipient_email' => $recipient,
                'subject' => $mailable->emailSubject,
                'type' => OutgoingEmailType::ReviewInvitation,
                'status' => OutgoingEmailStatus::Sent,
                'sent_at' => now(),
            ]);
        } catch (Throwable $e) {
            OutgoingEmail::create([
                'job_id' => $job->id,
                'client_id' => $job->client_id,
                'recipient_email' => $recipient,
                'subject' => $mailable->emailSubject,
                'type' => OutgoingEmailType::ReviewInvitation,
                'status' => OutgoingEmailStatus::Failed,
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function sendTestEmail(string $recipient): void
    {
        $this->applySmtpConfig();

        Mail::mailer('smtp')->raw(
            'This is a test email from Blog Workflow. Your SMTP settings are working correctly.',
            function ($message) use ($recipient) {
                $message->to($recipient)->subject('Blog Workflow — SMTP Test');
            }
        );
    }

    public function isConfigured(): bool
    {
        return (bool) Setting::get('smtp_host') && (bool) Setting::get('from_email');
    }

    private function applySmtpConfig(): void
    {
        Config::set('mail.mailers.smtp.host', Setting::get('smtp_host'));
        Config::set('mail.mailers.smtp.port', (int) (Setting::get('smtp_port') ?? 587));
        Config::set('mail.mailers.smtp.username', Setting::get('smtp_username'));
        Config::set('mail.mailers.smtp.password', Setting::get('smtp_password'));
        Config::set('mail.mailers.smtp.encryption', Setting::get('smtp_encryption') ?? 'tls');
        Config::set('mail.from.address', Setting::get('from_email'));
        Config::set('mail.from.name', Setting::get('from_name'));
    }
}
