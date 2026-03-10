<?php

namespace Arriendo\BugReport\Listeners;

use Arriendo\BugReport\Events\BugReportCreated;
use Arriendo\BugReport\Notifications\BugReportCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class SendBugReportNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(BugReportCreated $event): void
    {
        // Check if notifications are enabled
        if (! config('bug-report.notifications.enabled')) {
            return;
        }

        // Get configured recipients
        $recipients = config('bug-report.notifications.recipients', []);

        // Filter out empty values
        $recipients = array_filter($recipients);

        if (empty($recipients)) {
            return;
        }

        // Send notification to each recipient
        foreach ($recipients as $email) {
            Mail::to($email)->send(new BugReportCreatedNotification($event->bugReport));
        }
    }
}
