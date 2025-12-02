<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KanbanTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        
        return [
            'board_id' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'exists:kanban_boards,board_id'
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', 'in:low,medium,high,urgent'],
            'label' => ['nullable', 'in:ux,images,info,code_review,app,charts_maps,feature,bug'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'position' => ['nullable', 'integer', 'min:0'],
            'assignees' => ['nullable', 'array'],
            'assignees.*' => ['string', 'exists:employee_profiles,employee_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'board_id.required' => 'Board ID is required.',
            'board_id.exists' => 'The selected board does not exist.',
            'title.required' => 'Task title is required.',
            'title.max' => 'Task title cannot exceed 255 characters.',
            'priority.in' => 'Invalid priority level.',
            'label.in' => 'Invalid label selected.',
            'due_date.after_or_equal' => 'Due date must be today or a future date.',
            'assignees.*.exists' => 'One or more selected assignees are invalid.',
        ];
    }
}
