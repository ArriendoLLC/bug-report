<?php

namespace Arriendo\BugReport\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class InvalidFileUploadException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => $this->getMessage() ?: 'Invalid file upload',
            'code' => 'INVALID_FILE_UPLOAD',
        ], 422);
    }
}
