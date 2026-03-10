<?php

namespace Arriendo\BugReport\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class RateLimitExceededException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'Rate limit exceeded. Please try again later.',
            'code' => 'RATE_LIMIT_EXCEEDED',
        ], 429);
    }
}
