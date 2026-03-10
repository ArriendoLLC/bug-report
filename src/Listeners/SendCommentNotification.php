<?php

namespace Arriendo\BugReport\Listeners;

use Arriendo\BugReport\Events\CommentAdded;
use Arriendo\BugReport\Notifications\CommentAddedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendCommentNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(CommentAdded $event): void
    {
        // Check if notifications are enabled
        if (! config('bug-report.notifications.enabled')) {
            return;
        }

        // Get the bug report from the comment
        $bugReport = $event->comment->bugReport;

        if (! $bugReport) {
            return;
        }

        // Get the user who reported the bug
        $reporter = $bugReport->user;

        if (! $reporter || ! $reporter->email) {
            return;
        }

        // Don't send notification if the commenter is the reporter themselves
        if ($event->comment->user_id === $reporter->id) {
            return;
        }

        // Send notification to the bug reporter
        Mail::to($reporter->email)->send(
            new CommentAddedNotification($event->comment)
        );
    }
}
