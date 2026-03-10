<?php

namespace Arriendo\BugReport\Services;

use Arriendo\BugReport\Events\BugReportStatusChanged;
use Arriendo\BugReport\Models\BugReport;
use Illuminate\Support\Facades\DB;

class BugReportService
{
    public function __construct(
        protected AttachmentService $attachmentService
    ) {}

    /**
     * Create a new bug report with optional attachments.
     *
     * @param  array  $data
     * @param  array  $files
     * @return BugReport
     */
    public function create(array $data, array $files = []): BugReport
    {
        return DB::transaction(function () use ($data, $files) {
            // Create bug report
            $report = BugReport::create($data);

            // Store attachments if provided
            if (! empty($files)) {
                $this->attachmentService->storeFiles($files, $report);
            }

            // Load relationships for response
            $report->load(['user', 'attachments']);

            return $report;
        });
    }

    /**
     * Update an existing bug report.
     *
     * @param  BugReport  $report
     * @param  array  $data
     * @return BugReport
     */
    public function update(BugReport $report, array $data): BugReport
    {
        $report->update($data);

        return $report->fresh(['user', 'attachments', 'comments']);
    }

    /**
     * Update bug report status and dispatch event.
     *
     * @param  BugReport  $report
     * @param  string  $status
     * @return BugReport
     */
    public function updateStatus(BugReport $report, string $status): BugReport
    {
        $oldStatus = $report->status;

        // Update status
        $report->update(['status' => $status]);

        // Dispatch event if status actually changed
        if ($oldStatus !== $status) {
            event(new BugReportStatusChanged($report, $oldStatus));
        }

        return $report->fresh(['user', 'attachments', 'comments']);
    }

    /**
     * Soft delete a bug report.
     *
     * @param  BugReport  $report
     * @return bool
     */
    public function delete(BugReport $report): bool
    {
        return $report->delete();
    }
}
