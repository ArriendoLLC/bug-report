<?php

namespace Arriendo\BugReport;

use Arriendo\BugReport\Events\BugReportCreated;
use Arriendo\BugReport\Events\BugReportStatusChanged;
use Arriendo\BugReport\Events\CommentAdded;
use Arriendo\BugReport\Listeners\SendBugReportNotification;
use Arriendo\BugReport\Listeners\SendCommentNotification;
use Arriendo\BugReport\Listeners\SendStatusChangeNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class BugReportEventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        BugReportCreated::class => [
            SendBugReportNotification::class,
        ],
        BugReportStatusChanged::class => [
            SendStatusChangeNotification::class,
        ],
        CommentAdded::class => [
            SendCommentNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
