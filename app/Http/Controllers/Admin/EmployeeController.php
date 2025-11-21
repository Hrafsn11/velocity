<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Services\EmployeeService;
use Exception;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Display a listing of employees.
     */
    public function index()
    {
        $employees = $this->employeeService->getAllEmployees();
        $stats = $this->employeeService->getEmployeeStats();

        return view('admin.employees.index', [
            'employees' => $employees,
            'stats' => $stats,
        ]);
    }

    /**
     * Store a newly created employee.
     */
    public function store(EmployeeRequest $request)
    {
        try {
            $employee = $this->employeeService->createEmployee($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully!',
                'data' => [
                    'user_id' => $employee->user_id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'avatar' => $employee->avatar,
                    'role' => $employee->employeeProfile->role,
                    'specialization' => $employee->employeeProfile->specialization,
                    'level' => $employee->employeeProfile->level,
                    'skills' => $employee->employeeProfile->skills,
                    'status' => $employee->employeeProfile->status,
                    'phone' => $employee->employeeProfile->phone,
                    'role_icon' => $employee->employeeProfile->role_icon,
                    'level_color' => $employee->employeeProfile->level_color,
                    'status_badge' => $employee->employeeProfile->status_badge,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create employee: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit($employee)
    {
        try {
            $employee = $this->employeeService->findEmployee($employee);

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user_id' => $employee->user_id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'role' => $employee->employeeProfile->role,
                    'specialization' => $employee->employeeProfile->specialization,
                    'level' => $employee->employeeProfile->level,
                    'skills' => implode(', ', $employee->employeeProfile->skills),
                    'status' => $employee->employeeProfile->status,
                    'phone' => $employee->employeeProfile->phone,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch employee: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified employee.
     */
    public function update(EmployeeRequest $request, $employee)
    {
        try {
            $employee = $this->employeeService->findEmployee($employee);

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found',
                ], 404);
            }

            $updatedEmployee = $this->employeeService->updateEmployee($employee, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully!',
                'data' => [
                    'user_id' => $updatedEmployee->user_id,
                    'name' => $updatedEmployee->name,
                    'email' => $updatedEmployee->email,
                    'avatar' => $updatedEmployee->avatar,
                    'role' => $updatedEmployee->employeeProfile->role,
                    'specialization' => $updatedEmployee->employeeProfile->specialization,
                    'level' => $updatedEmployee->employeeProfile->level,
                    'skills' => $updatedEmployee->employeeProfile->skills,
                    'status' => $updatedEmployee->employeeProfile->status,
                    'phone' => $updatedEmployee->employeeProfile->phone,
                    'role_icon' => $updatedEmployee->employeeProfile->role_icon,
                    'level_color' => $updatedEmployee->employeeProfile->level_color,
                    'status_badge' => $updatedEmployee->employeeProfile->status_badge,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update employee: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified employee.
     */
    public function destroy($employee)
    {
        try {
            $employee = $this->employeeService->findEmployee($employee);

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found',
                ], 404);
            }

            $this->employeeService->deleteEmployee($employee);

            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete employee: ' . $e->getMessage(),
            ], 500);
        }
    }
}
