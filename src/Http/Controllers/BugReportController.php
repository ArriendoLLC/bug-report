<?php

namespace Arriendo\BugReport\Http\Controllers;

use Arriendo\BugReport\Http\Requests\StoreBugReportRequest;
use Arriendo\BugReport\Services\BugReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class BugReportController extends Controller
{
    public function __construct(
        protected BugReportService $bugReportService
    ) {}

    /**
     * Create a new bug report with optional attachments.
     */
    public function store(StoreBugReportRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Add authenticated user ID
        $data['user_id'] = $request->user()->id;

        // Get attachments if provided
        $files = $request->hasFile('attachments') ? $request->file('attachments') : [];

        // Create bug report with attachments
        $bugReport = $this->bugReportService->create($data, $files);

        return response()->json([
            'success' => true,
            'data' => $bugReport,
            'message' => 'Bug report created successfully',
        ], 201);
    }
}
