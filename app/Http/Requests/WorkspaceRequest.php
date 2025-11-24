<?php

namespace App\Http\Requests;

use App\Enums\WorkspaceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkspaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'manager_id' => ['required', 'exists:employee_profiles,employee_id'],
            'members' => ['nullable', 'array'],
            'members.*' => ['required', 'distinct', 'exists:employee_profiles,employee_id'],
            'status' => ['required', Rule::enum(WorkspaceStatus::class)],
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);

        if (isset($data['status']) && !$data['status'] instanceof WorkspaceStatus) {
            $data['status'] = WorkspaceStatus::from($data['status']);
        }

        return $data;
    }
}

