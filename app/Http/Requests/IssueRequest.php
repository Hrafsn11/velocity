<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workspace_id' => ['required', 'exists:workspaces,workspace_id'],
            'risk_id' => ['nullable', 'exists:risks,risk_id'],
            'linked_task_id' => ['nullable', 'exists:kanban_tasks,task_id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'integer', 'min:1', 'max:5'],
            'severity' => ['required', 'integer', 'min:1', 'max:5'],
            'status' => ['nullable', 'string', Rule::in(['open', 'in_progress', 'resolved', 'closed', 'reopened'])],
            'assignee_id' => ['nullable', 'exists:employee_profiles,employee_id'],
            'deadline' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'workspace_id.required' => 'Workspace is required',
            'workspace_id.exists' => 'Selected workspace does not exist',
            'title.required' => 'Issue title is required',
            'priority.required' => 'Priority is required',
            'severity.required' => 'Severity is required',
            'assignee_id.exists' => 'Selected assignee does not exist',
            'deadline.after_or_equal' => 'Deadline must be today or later',
        ];
    }
}
