<?php

namespace Arriendo\BugReport\Http\Requests;

use Arriendo\BugReport\Enums\BugReportStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization delegated to consuming app
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(BugReportStatus::values())],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $validStatuses = implode(', ', BugReportStatus::values());

        return [
            'status.required' => 'A status is required.',
            'status.in' => "The status must be one of: {$validStatuses}.",
        ];
    }
}
