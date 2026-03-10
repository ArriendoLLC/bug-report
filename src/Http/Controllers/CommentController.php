<?php

namespace Arriendo\BugReport\Http\Controllers;

use Arriendo\BugReport\Exceptions\BugReportNotFoundException;
use Arriendo\BugReport\Http\Requests\StoreCommentRequest;
use Arriendo\BugReport\Http\Requests\UpdateCommentRequest;
use Arriendo\BugReport\Models\BugReport;
use Arriendo\BugReport\Models\BugReportComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class CommentController extends Controller
{
    /**
     * Add a comment to a bug report.
     */
    public function store(StoreCommentRequest $request, int $reportId): JsonResponse
    {
        $report = BugReport::find($reportId);

        if (! $report) {
            throw new BugReportNotFoundException();
        }

        $comment = BugReportComment::create([
            'bug_report_id' => $reportId,
            'user_id' => $request->user()->id,
            'comment' => $request->validated('comment'),
        ]);

        // Load user relationship
        $comment->load('user');

        return response()->json([
            'success' => true,
            'data' => $comment,
            'message' => 'Comment added successfully',
        ], 201);
    }

    /**
     * Update a comment.
     */
    public function update(UpdateCommentRequest $request, int $reportId, int $commentId): JsonResponse
    {
        $report = BugReport::find($reportId);

        if (! $report) {
            throw new BugReportNotFoundException();
        }

        $comment = BugReportComment::where('id', $commentId)
            ->where('bug_report_id', $reportId)
            ->first();

        if (! $comment) {
            return response()->json([
                'success' => false,
                'error' => 'Comment not found',
                'code' => 'COMMENT_NOT_FOUND',
            ], 404);
        }

        $comment->update([
            'comment' => $request->validated('comment'),
        ]);

        // Load user relationship
        $comment->load('user');

        return response()->json([
            'success' => true,
            'data' => $comment,
            'message' => 'Comment updated successfully',
        ]);
    }

    /**
     * Soft delete a comment.
     */
    public function destroy(int $reportId, int $commentId): JsonResponse
    {
        $report = BugReport::find($reportId);

        if (! $report) {
            throw new BugReportNotFoundException();
        }

        $comment = BugReportComment::where('id', $commentId)
            ->where('bug_report_id', $reportId)
            ->first();

        if (! $comment) {
            return response()->json([
                'success' => false,
                'error' => 'Comment not found',
                'code' => 'COMMENT_NOT_FOUND',
            ], 404);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully',
        ]);
    }
}
