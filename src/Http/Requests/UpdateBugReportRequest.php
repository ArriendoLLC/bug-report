<?php

namespace Arriendo\BugReport\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBugReportRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'url' => ['sometimes', 'nullable', 'url', 'max:2048'],
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
            'title.max' => 'The title must not exceed 255 characters.',
            'url.url' => 'The URL must be a valid URL.',
            'url.max' => 'The URL must not exceed 2048 characters.',
        ];
    }
}
