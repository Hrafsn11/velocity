<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SprintController extends Controller
{
    public function board()
    {
        // Simulasi Data Sprint
        $sprintInfo = [
            'name' => 'Sprint 24: Payment Gateway Integration',
            'goal' => 'Menyelesaikan integrasi Midtrans dan memperbaiki bug checkout.',
            'start_date' => '10 Nov 2025',
            'end_date' => '24 Nov 2025',
            'days_left' => 4,
            'total_points' => 42,
            'completed_points' => 18
        ];

        // Data Board (Format jKanban)
        $boards = [
            [
                'id' => 'todo',
                'title' => 'To Do',
                'item' => [
                    [
                        'id' => 'task-1',
                        'title' => 'Research Midtrans API Documentation',
                        'badge' => 'Documentation',
                        'badge_color' => 'secondary',
                        'image' => null, // Bisa diisi path gambar
                        'assignees' => ['1.png', '5.png'],
                        'attachments' => 2,
                        'comments' => 4
                    ],
                    [
                        'id' => 'task-2',
                        'title' => 'Create Database Schema for Transactions',
                        'badge' => 'Backend',
                        'badge_color' => 'info',
                        'image' => null,
                        'assignees' => ['3.png'],
                        'attachments' => 0,
                        'comments' => 1
                    ]
                ]
            ],
            [
                'id' => 'progress',
                'title' => 'In Progress',
                'item' => [
                    [
                        'id' => 'task-3',
                        'title' => 'Fix: Cart items not updating quantity',
                        'badge' => 'Bug',
                        'badge_color' => 'danger',
                        'image' => 'assets/img/elements/2.jpg', // Contoh task dengan gambar
                        'assignees' => ['2.png'],
                        'attachments' => 1,
                        'comments' => 12
                    ]
                ]
            ],
            [
                'id' => 'review',
                'title' => 'Code Review',
                'item' => [
                    [
                        'id' => 'task-4',
                        'title' => 'Frontend Checkout UI implementation',
                        'badge' => 'Frontend',
                        'badge_color' => 'warning',
                        'image' => null,
                        'assignees' => ['6.png', '1.png'],
                        'attachments' => 5,
                        'comments' => 3
                    ]
                ]
            ],
            [
                'id' => 'done',
                'title' => 'Done',
                'item' => [
                    [
                        'id' => 'task-5',
                        'title' => 'Setup Project Repository',
                        'badge' => 'DevOps',
                        'badge_color' => 'success',
                        'image' => null,
                        'assignees' => ['1.png'],
                        'attachments' => 0,
                        'comments' => 0
                    ]
                ]
            ]
        ];

        return view('workspace.index', compact('boards', 'sprintInfo'));
    }
}