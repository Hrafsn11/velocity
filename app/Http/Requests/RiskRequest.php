<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RiskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workspace_id' => ['required', 'exists:workspaces,workspace_id'],
            'description' => ['required', 'string'],
            'cause' => ['nullable', 'string'],
            'category' => ['required', 'string', Rule::in(['Technical', 'SDM', 'Financial', 'Timeline'])],
            'affected_module' => ['nullable', 'string', 'max:100'],
            'probability' => ['required', 'integer', 'min:1', 'max:5'],
            'impact' => ['required', 'integer', 'min:1', 'max:5'],
            'status' => ['nullable', 'string', Rule::in(['active', 'monitoring', 'mitigated', 'materialized', 'closed'])],
            'mitigation_actions' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'workspace_id.required' => 'Workspace is required',
            'workspace_id.exists' => 'Selected workspace does not exist',
            'description.required' => 'Risk description is required',
            'category.required' => 'Risk category is required',
            'probability.required' => 'Probability is required',
            'probability.min' => 'Probability must be between 1 and 5',
            'probability.max' => 'Probability must be between 1 and 5',
            'impact.required' => 'Impact is required',
            'impact.min' => 'Impact must be between 1 and 5',
            'impact.max' => 'Impact must be between 1 and 5',
        ];
    }
}
