<?php

namespace Arriendo\BugReport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBugReportRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'url' => ['nullable', 'url', 'max:2048'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize HTML in description
        if ($this->has('description')) {
            $this->merge([
                'description' => strip_tags($this->input('description'), '<p><br><strong><em><ul><ol><li><a>'),
            ]);
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'A title is required for the bug report.',
            'title.max' => 'The title must not exceed 255 characters.',
            'description.required' => 'A description is required for the bug report.',
            'url.url' => 'The URL must be a valid URL.',
            'url.max' => 'The URL must not exceed 2048 characters.',
            'attachments.array' => 'Attachments must be provided as an array.',
            'attachments.*.file' => 'Each attachment must be a valid file.',
        ];
    }
}
