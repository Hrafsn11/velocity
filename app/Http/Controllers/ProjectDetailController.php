<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectDetailController extends Controller
{
    public function show($id = 1)
    {
        // Dummy Data Project
        $project = [
            'id' => $id,
            'name' => 'Velocity App - Phase 2',
            'status' => 'On Track', // On Track, At Risk, Off Track
            'description' => 'Pengembangan fitur lanjutan untuk modul pembayaran dan integrasi pihak ketiga.',
            'members' => ['1.png', '2.png', '3.png', '4.png'],
            'start_date' => '01 Nov 2025',
            'due_date' => '30 Dec 2025',
            'progress' => 65,
            // Dummy Activity for Sidebar
            'activities' => [
                ['user' => 'Jordan Smith', 'action' => 'completed task', 'target' => 'Database Migration', 'time' => '2 mins ago'],
                ['user' => 'Viola Amherd', 'action' => 'attached file', 'target' => 'Design_v2.fig', 'time' => '1 hour ago'],
                ['user' => 'System', 'action' => 'created project', 'target' => 'Velocity App', 'time' => '2 days ago'],
            ]
        ];

        return view('workspace.detail', compact('project'));
    }
}