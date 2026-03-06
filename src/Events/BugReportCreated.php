<?php

namespace Arriendo\BugReport\Events;

use Arriendo\BugReport\Models\BugReport;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BugReportCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * The bug report instance.
     *
     * @var BugReport
     */
    public BugReport $bugReport;

    /**
     * Create a new event instance.
     */
    public function __construct(BugReport $bugReport)
    {
        $this->bugReport = $bugReport;
    }
}
