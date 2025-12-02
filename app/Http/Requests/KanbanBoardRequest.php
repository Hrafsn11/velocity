<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KanbanBoardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workspace_id' => ['required', 'string', 'exists:workspaces,workspace_id'],
            'title' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'position' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'workspace_id.required' => 'Workspace ID is required.',
            'workspace_id.exists' => 'The selected workspace does not exist.',
            'title.required' => 'Board title is required.',
            'title.max' => 'Board title cannot exceed 255 characters.',
            'color.regex' => 'Color must be a valid hex color code.',
        ];
    }
}
