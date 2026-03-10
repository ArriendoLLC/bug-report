<?php

namespace Arriendo\BugReport\Listeners;

use Arriendo\BugReport\Events\BugReportStatusChanged;
use Arriendo\BugReport\Notifications\BugReportStatusChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendStatusChangeNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(BugReportStatusChanged $event): void
    {
        // Check if notifications are enabled
        if (! config('bug-report.notifications.enabled')) {
            return;
        }

        // Get the user who reported the bug
        $reporter = $event->bugReport->user;

        if (! $reporter || ! $reporter->email) {
            return;
        }

        // Send notification to the bug reporter
        Mail::to($reporter->email)->send(
            new BugReportStatusChangedNotification($event->bugReport, $event->oldStatus)
        );
    }
}
