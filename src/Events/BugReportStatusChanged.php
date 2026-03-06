<?php

namespace Arriendo\BugReport\Events;

use Arriendo\BugReport\Enums\BugReportStatus;
use Arriendo\BugReport\Models\BugReport;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BugReportStatusChanged
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
     * The old status.
     *
     * @var BugReportStatus
     */
    public BugReportStatus $oldStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(BugReport $bugReport, BugReportStatus $oldStatus)
    {
        $this->bugReport = $bugReport;
        $this->oldStatus = $oldStatus;
    }
}
