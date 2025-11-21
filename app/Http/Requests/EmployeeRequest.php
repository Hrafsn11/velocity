<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $userId = null;
        if ($isUpdate) {
            $employeeParam = $this->route('employee');
            if ($employeeParam) {
                $profile = \App\Models\EmployeeProfile::where('employee_id', $employeeParam)->first();
                $userId = $profile?->user_id;
            }
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            // When excluding the current user from unique rule, specify the id column `user_id`.
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$userId},user_id"],
            'password' => [$isUpdate ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:programmer,designer,qa,analyst,manager'],
            'specialization' => ['required', 'string', 'max:255'],
            'level' => ['required', 'in:intern,junior,middle,senior,lead'],
            'skills' => ['required', 'string'],
            'status' => ['required', 'in:available,unavailable'],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Employee name is required',
            'email.required' => 'Email address is required',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'skills.required' => 'Please enter at least one skill',
        ];
    }
}
