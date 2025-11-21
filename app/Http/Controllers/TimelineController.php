<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimelineController extends Controller
{
    public function index()
    {
        // Dummy Data Projects
        $projects = [
            [
                'id' => 1,
                'name' => 'Velocity App v1.0',
                'client' => 'Internal',
                'start' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'end' => Carbon::now()->addDays(20)->format('Y-m-d'),
                'status' => 'In Progress',
                'color' => '#7367f0', // Primary
                'team' => ['1.png', '2.png', '3.png']
            ],
            [
                'id' => 2,
                'name' => 'E-Commerce Revamp',
                'client' => 'PT. Maju Mundur',
                'start' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'end' => Carbon::now()->addDays(45)->format('Y-m-d'),
                'status' => 'In Progress',
                'color' => '#00cfe8', // Info
                'team' => ['5.png', '6.png']
            ],
            [
                'id' => 3,
                'name' => 'Finance Module API',
                'client' => 'Bank ABC',
                'start' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'end' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'status' => 'Upcoming',
                'color' => '#ff9f43', // Warning
                'team' => ['9.png']
            ],
            [
                'id' => 4,
                'name' => 'Bug Fixing Sprint 24',
                'client' => 'Internal',
                'start' => Carbon::now()->subDays(15)->format('Y-m-d'),
                'end' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'status' => 'Completed',
                'color' => '#28c76f', // Success
                'team' => ['1.png', '5.png']
            ],
            [
                'id' => 5,
                'name' => 'Mobile App Design',
                'client' => 'Startup X',
                'start' => Carbon::now()->format('Y-m-d'),
                'end' => Carbon::now()->addDays(15)->format('Y-m-d'),
                'status' => 'In Progress',
                'color' => '#ea5455', // Danger
                'team' => ['2.png']
            ],
        ];

        return view('admin.timeline.index', compact('projects'));
    }
}