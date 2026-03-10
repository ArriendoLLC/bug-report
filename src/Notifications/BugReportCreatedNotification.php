<?php

namespace Arriendo\BugReport\Notifications;

use Arriendo\BugReport\Models\BugReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BugReportCreatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public BugReport $bugReport
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Bug Report: '.$this->bugReport->title,
            from: new Address(
                config('bug-report.notifications.from_address', config('mail.from.address')),
                config('bug-report.notifications.from_name', 'Bug Report System')
            ),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'bug-report::emails.bug-created',
            with: [
                'bugReport' => $this->bugReport,
                'reporter' => $this->bugReport->user,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
