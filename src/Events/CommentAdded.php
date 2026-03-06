<?php

namespace Arriendo\BugReport\Events;

use Arriendo\BugReport\Models\BugReportComment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentAdded
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * The comment instance.
     *
     * @var BugReportComment
     */
    public BugReportComment $comment;

    /**
     * Create a new event instance.
     */
    public function __construct(BugReportComment $comment)
    {
        $this->comment = $comment;
    }
}
