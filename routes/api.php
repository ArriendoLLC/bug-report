<?php

use Arriendo\BugReport\Http\Controllers\BugReportAdminController;
use Arriendo\BugReport\Http\Controllers\BugReportController;
use Arriendo\BugReport\Http\Controllers\CommentController;
use Arriendo\BugReport\Http\Middleware\BugReportRateLimit;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Bug Report Package API Routes
|--------------------------------------------------------------------------
|
| These routes are registered by the bug-report package install command.
| They will be appended to your application's routes/api.php file.
|
| IMPORTANT: Admin routes should be protected with appropriate middleware
| in your consuming application (e.g., 'auth:sanctum', 'can:admin', etc.)
|
*/

$prefix = config('bug-report.route_prefix', 'bug-reports');

Route::prefix($prefix)->group(function () {
    /*
    |--------------------------------------------------------------------------
    | User Routes (Authenticated)
    |--------------------------------------------------------------------------
    |
    | These routes allow authenticated users to submit bug reports.
    | Apply your application's authentication middleware (e.g., 'auth:sanctum')
    |
    */

    // User: Submit bug report (with rate limiting)
    Route::post('/', [BugReportController::class, 'store'])
        ->middleware(['auth:sanctum', BugReportRateLimit::class])
        ->name('bug-reports.store');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (Authenticated + Admin Middleware)
    |--------------------------------------------------------------------------
    |
    | These routes should be protected with BOTH authentication AND admin
    | authorization middleware. Examples:
    |   - middleware(['auth:sanctum', 'can:manage-bug-reports'])
    |   - middleware(['auth:sanctum', 'role:admin'])
    |   - middleware(['auth:sanctum', 'admin'])
    |
    | Update the middleware array below to match your application's
    | authorization strategy.
    |
    */

    Route::middleware(['auth:sanctum' /* Add your admin middleware here */])->group(function () {
        // Admin: List all bug reports (with pagination & filtering)
        Route::get('/', [BugReportAdminController::class, 'index'])
            ->name('bug-reports.index');

        // Admin: View single bug report
        Route::get('/{id}', [BugReportAdminController::class, 'show'])
            ->name('bug-reports.show');

        // Admin: Update bug report status
        Route::put('/{id}/status', [BugReportAdminController::class, 'updateStatus'])
            ->name('bug-reports.update-status');

        // Admin: Soft delete bug report
        Route::delete('/{id}', [BugReportAdminController::class, 'destroy'])
            ->name('bug-reports.destroy');

        /*
        |--------------------------------------------------------------------------
        | Comment Routes (Admin Only)
        |--------------------------------------------------------------------------
        */

        // Admin: Add comment to bug report
        Route::post('/{reportId}/comments', [CommentController::class, 'store'])
            ->name('bug-reports.comments.store');

        // Admin: Update comment
        Route::put('/{reportId}/comments/{commentId}', [CommentController::class, 'update'])
            ->name('bug-reports.comments.update');

        // Admin: Soft delete comment
        Route::delete('/{reportId}/comments/{commentId}', [CommentController::class, 'destroy'])
            ->name('bug-reports.comments.destroy');
    });
});
