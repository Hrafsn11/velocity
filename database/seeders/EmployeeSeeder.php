<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\EmployeeProfile;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $employees = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@velocity.com',
                'avatar' => '/assets/img/avatars/1.png',
                'role' => 'programmer',
                'specialization' => 'Full Stack Developer',
                'level' => 'senior',
                'skills' => ['Laravel', 'Vue.js', 'MySQL', 'Docker'],
                'status' => 'available',
                'phone' => '+62 812-3456-7890',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@velocity.com',
                'avatar' => '/assets/img/avatars/2.png',
                'role' => 'designer',
                'specialization' => 'UI/UX Designer',
                'level' => 'senior',
                'skills' => ['Figma', 'Adobe XD', 'Sketch', 'Prototyping'],
                'status' => 'available',
                'phone' => '+62 813-4567-8901',
            ],
            [
                'name' => 'Michael Johnson',
                'email' => 'michael.j@velocity.com',
                'avatar' => '/assets/img/avatars/3.png',
                'role' => 'programmer',
                'specialization' => 'Frontend Developer',
                'level' => 'middle',
                'skills' => ['React', 'TypeScript', 'Tailwind', 'Next.js'],
                'status' => 'available',
                'phone' => '+62 814-5678-9012',
            ],
            [
                'name' => 'Sarah Williams',
                'email' => 'sarah.w@velocity.com',
                'avatar' => '/assets/img/avatars/4.png',
                'role' => 'qa',
                'specialization' => 'QA Engineer',
                'level' => 'middle',
                'skills' => ['Selenium', 'Jest', 'Cypress', 'Manual Testing'],
                'status' => 'available',
                'phone' => '+62 815-6789-0123',
            ],
            [
                'name' => 'David Brown',
                'email' => 'david.b@velocity.com',
                'avatar' => '/assets/img/avatars/5.png',
                'role' => 'programmer',
                'specialization' => 'Backend Developer',
                'level' => 'lead',
                'skills' => ['Node.js', 'PostgreSQL', 'Redis', 'Microservices'],
                'status' => 'unavailable',
                'phone' => '+62 816-7890-1234',
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.d@velocity.com',
                'avatar' => '/assets/img/avatars/6.png',
                'role' => 'designer',
                'specialization' => 'Product Designer',
                'level' => 'middle',
                'skills' => ['User Research', 'Wireframing', 'Figma', 'Design Systems'],
                'status' => 'available',
                'phone' => '+62 817-8901-2345',
            ],
            [
                'name' => 'Robert Miller',
                'email' => 'robert.m@velocity.com',
                'avatar' => '/assets/img/avatars/7.png',
                'role' => 'analyst',
                'specialization' => 'System Analyst',
                'level' => 'senior',
                'skills' => ['Requirements Analysis', 'UML', 'Business Process', 'SQL'],
                'status' => 'available',
                'phone' => '+62 818-9012-3456',
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.a@velocity.com',
                'avatar' => '/assets/img/avatars/8.png',
                'role' => 'programmer',
                'specialization' => 'Mobile Developer',
                'level' => 'middle',
                'skills' => ['Flutter', 'Dart', 'Firebase', 'React Native'],
                'status' => 'available',
                'phone' => '+62 819-0123-4567',
            ],
            [
                'name' => 'James Wilson',
                'email' => 'james.w@velocity.com',
                'avatar' => '/assets/img/avatars/9.png',
                'role' => 'manager',
                'specialization' => 'Project Manager',
                'level' => 'lead',
                'skills' => ['Agile', 'Scrum', 'Jira', 'Team Management'],
                'status' => 'available',
                'phone' => '+62 820-1234-5678',
            ],
            [
                'name' => 'Jennifer Taylor',
                'email' => 'jennifer.t@velocity.com',
                'avatar' => '/assets/img/avatars/10.png',
                'role' => 'programmer',
                'specialization' => 'DevOps Engineer',
                'level' => 'senior',
                'skills' => ['AWS', 'Kubernetes', 'CI/CD', 'Terraform'],
                'status' => 'available',
                'phone' => '+62 821-2345-6789',
            ],
            [
                'name' => 'Christopher Moore',
                'email' => 'chris.m@velocity.com',
                'avatar' => '/assets/img/avatars/11.png',
                'role' => 'qa',
                'specialization' => 'Automation QA',
                'level' => 'junior',
                'skills' => ['Postman', 'JUnit', 'Selenium', 'API Testing'],
                'status' => 'available',
                'phone' => '+62 822-3456-7890',
            ],
            [
                'name' => 'Amanda Jackson',
                'email' => 'amanda.j@velocity.com',
                'avatar' => '/assets/img/avatars/12.png',
                'role' => 'designer',
                'specialization' => 'Graphic Designer',
                'level' => 'junior',
                'skills' => ['Photoshop', 'Illustrator', 'InDesign', 'Branding'],
                'status' => 'unavailable',
                'phone' => '+62 823-4567-8901',
            ],
        ];

        foreach ($employees as $employeeData) {
            $user = User::create([
                'name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'password' => Hash::make('password'),
                'avatar' => $employeeData['avatar'],
                'email_verified_at' => now(),
            ]);

            $user->employeeProfile()->create([
                'role' => $employeeData['role'],
                'specialization' => $employeeData['specialization'],
                'level' => $employeeData['level'],
                'skills' => $employeeData['skills'],
                'status' => $employeeData['status'],
                'phone' => $employeeData['phone'],
            ]);

            // Assign Employee role
            $user->assignRole('Employee');
        }
    }
}
