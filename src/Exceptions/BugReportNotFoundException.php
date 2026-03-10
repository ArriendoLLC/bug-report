<?php

namespace Arriendo\BugReport\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class BugReportNotFoundException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'Bug report not found',
            'code' => 'BUG_REPORT_NOT_FOUND',
        ], 404);
    }
}
