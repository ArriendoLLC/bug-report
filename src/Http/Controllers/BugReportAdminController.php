<?php

namespace Arriendo\BugReport\Http\Controllers;

use Arriendo\BugReport\Exceptions\BugReportNotFoundException;
use Arriendo\BugReport\Http\Requests\UpdateStatusRequest;
use Arriendo\BugReport\Models\BugReport;
use Arriendo\BugReport\Services\BugReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BugReportAdminController extends Controller
{
    public function __construct(
        protected BugReportService $bugReportService
    ) {}

    /**
     * List all bug reports with pagination and filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = config('bug-report.pagination.per_page', 15);

        $query = BugReport::with(['user', 'attachments'])
            ->withCount('comments');

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search by title or description
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Order by most recent first
        $query->orderBy('created_at', 'desc');

        // Paginate results
        $reports = $query->paginate($perPage);

        return response()->json($reports);
    }

    /**
     * View a single bug report with all relationships.
     */
    public function show(int $id): JsonResponse
    {
        $report = BugReport::with(['user', 'attachments', 'comments.user'])
            ->find($id);

        if (! $report) {
            throw new BugReportNotFoundException();
        }

        return response()->json([
            'success' => true,
            'data' => $report,
        ]);
    }

    /**
     * Update the status of a bug report.
     */
    public function updateStatus(UpdateStatusRequest $request, int $id): JsonResponse
    {
        $report = BugReport::find($id);

        if (! $report) {
            throw new BugReportNotFoundException();
        }

        $updatedReport = $this->bugReportService->updateStatus(
            $report,
            $request->validated('status')
        );

        return response()->json([
            'success' => true,
            'data' => $updatedReport,
            'message' => 'Bug report status updated successfully',
        ]);
    }

    /**
     * Soft delete a bug report.
     */
    public function destroy(int $id): JsonResponse
    {
        $report = BugReport::find($id);

        if (! $report) {
            throw new BugReportNotFoundException();
        }

        $this->bugReportService->delete($report);

        return response()->json([
            'success' => true,
            'message' => 'Bug report deleted successfully',
        ]);
    }
}
