<?php

namespace Database\Seeders;

use App\Models\KanbanBoard;
use App\Models\KanbanTask;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Database\Seeder;

class KanbanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first workspace
        $workspace = Workspace::first();
        
        if (!$workspace) {
            $this->command->error('No workspace found. Please create a workspace first.');
            return;
        }

        // Get a user with employee profile
        $user = User::whereHas('employeeProfile')->first();
        
        if (!$user || !$user->user_id) {
            $this->command->error('No valid user found. Please create users first.');
            return;
        }

        // Create boards
        $boards = [
            ['title' => 'To Do', 'color' => '#6366f1', 'position' => 0],
            ['title' => 'In Progress', 'color' => '#16a34a', 'position' => 1],
            ['title' => 'Done', 'color' => '#ea580c', 'position' => 2],
        ];

        foreach ($boards as $boardData) {
            $board = KanbanBoard::create([
                'workspace_id' => $workspace->workspace_id,
                'title' => $boardData['title'],
                'color' => $boardData['color'],
                'position' => $boardData['position'],
            ]);

            // Create sample tasks for each board
            for ($i = 1; $i <= 2; $i++) {
                KanbanTask::create([
                    'board_id' => $board->board_id,
                    'workspace_id' => $workspace->workspace_id,
                    'created_by' => $user->user_id,
                    'title' => $boardData['title'] . ' Task ' . $i,
                    'description' => 'This is a sample task for testing purposes',
                    'priority' => ['low', 'medium', 'high', 'urgent'][rand(0, 3)],
                    'label' => ['UX', 'Images', 'Info', 'Code Review', 'App', 'Feature', 'Bug'][rand(0, 6)],
                    'due_date' => now()->addDays(rand(1, 30)),
                    'position' => $i - 1,
                ]);
            }
        }

        $this->command->info('Kanban boards and tasks seeded successfully!');
    }
}
