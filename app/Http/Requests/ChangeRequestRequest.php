<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'workspace_id' => ['required', 'exists:workspaces,workspace_id'],
            'issue_id' => ['nullable', 'exists:issues,issue_id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', Rule::in(['timeline', 'resource'])],
            'justification' => ['required', 'string'],
        ];

        // Type-specific validation
        if ($this->type === 'timeline') {
            $rules['timeline_extension_days'] = ['required', 'integer', 'min:1', 'max:365'];
        }

        if ($this->type === 'resource') {
            $rules['members_to_add'] = ['nullable', 'array'];
            $rules['members_to_add.*'] = ['required', 'exists:employee_profiles,employee_id'];
            $rules['members_to_remove'] = ['nullable', 'array'];
            $rules['members_to_remove.*'] = ['required', 'exists:employee_profiles,employee_id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'workspace_id.required' => 'Workspace is required',
            'workspace_id.exists' => 'Selected workspace does not exist',
            'title.required' => 'Change request title is required',
            'type.required' => 'Change request type is required',
            'type.in' => 'Change request type must be either timeline or resource',
            'justification.required' => 'Justification is required',
            'timeline_extension_days.required' => 'Extension days is required for timeline change',
            'timeline_extension_days.min' => 'Extension must be at least 1 day',
            'timeline_extension_days.max' => 'Extension cannot exceed 365 days',
            'members_to_add.*.exists' => 'Selected member to add does not exist',
            'members_to_remove.*.exists' => 'Selected member to remove does not exist',
        ];
    }
}
