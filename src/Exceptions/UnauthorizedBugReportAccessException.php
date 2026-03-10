<?php

namespace Arriendo\BugReport\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class UnauthorizedBugReportAccessException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'Unauthorized access to bug report',
            'code' => 'UNAUTHORIZED_ACCESS',
        ], 403);
    }
}
