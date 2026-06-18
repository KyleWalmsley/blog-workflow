<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Job;
use App\Models\Setting;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewInvitation extends Mailable
{
    use SerializesModels;

    public string $emailSubject;
    public string $emailBody;

    public function __construct(public Job $job)
    {
        $template = EmailTemplate::forName('review_invitation');

        $this->emailSubject = $template?->subject ?? 'Your Blog Content Is Ready For Review';
        $this->emailBody = $template?->body ?? '';
    }

    public function envelope(): Envelope
    {
        $replyTo = Setting::get('reply_to_email') ?? Setting::get('from_email');

        return new Envelope(
            subject: $this->emailSubject,
            replyTo: $replyTo ? [new Address($replyTo)] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.review-invitation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
