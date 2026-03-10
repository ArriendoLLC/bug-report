<?php

namespace Arriendo\BugReport\Notifications;

use Arriendo\BugReport\Enums\BugReportStatus;
use Arriendo\BugReport\Models\BugReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BugReportStatusChangedNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public BugReport $bugReport,
        public BugReportStatus|string $oldStatus
    ) {
        // Convert string to enum if needed
        if (is_string($this->oldStatus)) {
            $this->oldStatus = BugReportStatus::tryFrom($this->oldStatus);
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bug Report Status Updated: '.$this->bugReport->title,
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
            view: 'bug-report::emails.status-changed',
            with: [
                'bugReport' => $this->bugReport,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->bugReport->status,
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
