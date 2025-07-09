<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tasks = [
            [
                'title' => 'Complete project documentation',
                'description' => 'Write comprehensive documentation for the new task management system including user guide and technical specifications.',
                'status' => 'in_progress',
                'due_date' => now()->addDays(3),
            ],
            [
                'title' => 'Review code changes',
                'description' => 'Review pull requests from team members and provide feedback on the recent feature implementations.',
                'status' => 'pending',
                'due_date' => now()->addDays(1),
            ],
            [
                'title' => 'Setup production environment',
                'description' => 'Configure the production server, setup database, and deploy the application.',
                'status' => 'pending',
                'due_date' => now()->addDays(7),
            ],
            [
                'title' => 'Bug fix: Login validation',
                'description' => 'Fix the login form validation issue reported by QA team.',
                'status' => 'completed',
                'due_date' => now()->subDays(1),
            ],
            [
                'title' => 'Team meeting preparation',
                'description' => 'Prepare agenda and materials for the upcoming team standup meeting.',
                'status' => 'completed',
                'due_date' => now()->subDays(2),
            ],
            [
                'title' => 'Database optimization',
                'description' => 'Analyze and optimize database queries to improve application performance.',
                'status' => 'pending',
                'due_date' => now()->addDays(10),
            ],
            [
                'title' => 'Client feedback implementation',
                'description' => 'Implement changes based on client feedback from the last demo session.',
                'status' => 'in_progress',
                'due_date' => now()->addDays(5),
            ],
            [
                'title' => 'Security audit',
                'description' => 'Conduct a comprehensive security audit of the application.',
                'status' => 'pending',
                'due_date' => null,
            ],
        ];

        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }
    }
}
