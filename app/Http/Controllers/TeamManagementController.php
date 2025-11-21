<?php

namespace App\Http\Controllers;

class TeamManagementController extends Controller
{
    // Di Controller (misal: TeamController.php)
    public function index()
    {
        // Contoh Data Dummy untuk Software House
        $teams = [
            [
                'id' => 1,
                'name' => 'Jordan Smith',
                'email' => 'jordan@velocity.com',
                'role' => 'Programmer',
                'specialization' => 'Backend Developer', // Info Baru
                'level' => 'Senior',
                'avatar' => 'assets/img/avatars/1.png',
                'status' => 'online',
                'projects_count' => 5,
                'skills' => ['Laravel', 'Node.js', 'Redis'], // Skill Backend
            ],
            [
                'id' => 2,
                'name' => 'Viola Amherd',
                'email' => 'viola@velocity.com',
                'role' => 'UI/UX Designer',
                'specialization' => 'Product Designer', // Info Baru
                'level' => 'Lead',
                'avatar' => 'assets/img/avatars/2.png',
                'status' => 'busy',
                'projects_count' => 3,
                'skills' => ['Figma', 'Prototyping', 'User Research'],
            ],
            [
                'id' => 3,
                'name' => 'Lula Barton',
                'email' => 'lula@velocity.com',
                'role' => 'System Analyst',
                'specialization' => 'Business Analyst', // Info Baru
                'level' => 'Middle',
                'avatar' => 'assets/img/avatars/3.png',
                'status' => 'offline',
                'projects_count' => 1,
                'skills' => ['SQL', 'UML', 'Jira'],
            ],
            [
                'id' => 4,
                'name' => 'Herman Rees',
                'email' => 'herman@velocity.com',
                'role' => 'Programmer',
                'specialization' => 'Frontend Developer', // Info Baru
                'level' => 'Junior',
                'avatar' => 'assets/img/avatars/5.png',
                'status' => 'online',
                'projects_count' => 2,
                'skills' => ['Vue.js', 'Tailwind', 'React'], // Skill Frontend
            ],
            [
                'id' => 5,
                'name' => 'Sue Shei',
                'email' => 'sue@velocity.com',
                'role' => 'QA Engineer',
                'specialization' => 'Automation Tester', // Info Baru
                'level' => 'Middle',
                'avatar' => 'assets/img/avatars/6.png',
                'status' => 'online',
                'projects_count' => 4,
                'skills' => ['Selenium', 'Cypress', 'Manual'],
            ],
        ];

        $stats = [
            'total' => count($teams),
            'busy' => collect($teams)->where('status', 'busy')->count(),
            'available' => collect($teams)->where('status', 'online')->count(),
            'offline' => collect($teams)->where('status', 'offline')->count(),
        ];

        return view('admin.team-management.index', compact('teams', 'stats'));
    }
}
