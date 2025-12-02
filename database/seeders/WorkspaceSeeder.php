<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorkspaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first user with manager role and employee profile
        $manager = User::whereHas('employeeProfile', function($query) {
            $query->where('role', 'manager');
        })->first();

        if (!$manager || !$manager->employeeProfile) {
            $this->command->error('No manager with employee profile found. Please run EmployeeSeeder first.');
            return;
        }

        // Create sample workspaces
        $workspaces = [
            [
                'title' => 'Velocity Platform Development',
                'description' => 'Main platform development workspace for building the Velocity project management system',
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addMonths(6),
            ],
            [
                'title' => 'Mobile App Project',
                'description' => 'Development workspace for iOS and Android mobile applications',
                'status' => 'active',
                'start_date' => now()->subDays(15),
                'end_date' => now()->addMonths(4),
            ],
            [
                'title' => 'Marketing Website Redesign',
                'description' => 'Workspace for redesigning the company marketing website',
                'status' => 'planning',
                'start_date' => now()->addWeek(),
                'end_date' => now()->addMonths(3),
            ],
        ];

        foreach ($workspaces as $workspaceData) {
            $workspace = Workspace::create([
                'title' => $workspaceData['title'],
                'description' => $workspaceData['description'],
                'manager_id' => $manager->employeeProfile->employee_id,
                'status' => $workspaceData['status'],
                'start_date' => $workspaceData['start_date'],
                'end_date' => $workspaceData['end_date'],
            ]);

            // Add members to workspace (using employee_id, not user_id)
            $memberProfiles = \App\Models\EmployeeProfile::inRandomOrder()
                ->limit(rand(4, 8))
                ->get();

            foreach ($memberProfiles as $profile) {
                $workspace->members()->attach($profile->employee_id);
            }
        }

        $this->command->info('Workspaces seeded successfully!');
    }
}
