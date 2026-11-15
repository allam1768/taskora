<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@taskora.test'],
            [
                'name' => 'Admin Taskora',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        $member = User::firstOrCreate(
            ['email' => 'member@taskora.test'],
            [
                'name' => 'Member Taskora',
                'password' => Hash::make('password123'),
                'role' => 'member',
            ]
        );

        if (Task::count() > 0) {
            return;
        }

        $now = now();

        $tasks = [
            [
                'title' => 'Design System & Navigation Shell',
                'description' => 'Build modern sidebar navigation, top header, and responsive layout for Taskora.',
                'status' => 'done',
                'priority' => 'high',
                'due_date' => $now->copy()->subDays(1)->toDateString(),
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Authentication & Role Gate Integration',
                'description' => 'Implement secure authentication, session handling, and member/admin role gates.',
                'status' => 'done',
                'priority' => 'high',
                'due_date' => $now->copy()->subDays(2)->toDateString(),
                'user_id' => $member->id,
            ],
            [
                'title' => 'Interactive Dashboard Statistics & Metrics',
                'description' => 'Create real-time metric cards for Total, To Do, In Progress, and Completed tasks.',
                'status' => 'done',
                'priority' => 'medium',
                'due_date' => $now->copy()->addDays(1)->toDateString(),
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Realtime Task Search & Filter System',
                'description' => 'Implement reactive search by title with status and priority multi-filtering.',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => $now->copy()->addDays(2)->toDateString(),
                'user_id' => $member->id,
            ],
            [
                'title' => 'Database Schema Migration for Tasks',
                'description' => 'Finalize task table columns, foreign keys to users, status enums, and deadlines.',
                'status' => 'in_progress',
                'priority' => 'high',
                'due_date' => $now->copy()->addDays(3)->toDateString(),
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Deadline Reminder & Alert Notifications',
                'description' => 'Add visual badges for upcoming deadlines and notify team members before due dates.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'due_date' => $now->copy()->addDays(4)->toDateString(),
                'user_id' => $member->id,
            ],
            [
                'title' => 'Task Priority Sorting & Tagging',
                'description' => 'Support sorting tasks by priority (High, Medium, Low) and custom tags.',
                'status' => 'todo',
                'priority' => 'low',
                'due_date' => $now->copy()->addDays(6)->toDateString(),
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Team Collaboration Comments Thread',
                'description' => 'Enable team discussions and comment history directly on task detail views.',
                'status' => 'todo',
                'priority' => 'medium',
                'due_date' => $now->copy()->addDays(7)->toDateString(),
                'user_id' => $member->id,
            ],
            [
                'title' => 'File Attachments & Asset Uploads',
                'description' => 'Allow members to upload screenshots, documents, and specifications to tasks.',
                'status' => 'todo',
                'priority' => 'low',
                'due_date' => $now->copy()->addDays(9)->toDateString(),
                'user_id' => $admin->id,
            ],
            [
                'title' => 'Export Task Reports to PDF and CSV',
                'description' => 'Generate downloadable summary reports of team productivity and completion rate.',
                'status' => 'todo',
                'priority' => 'low',
                'due_date' => $now->copy()->addDays(12)->toDateString(),
                'user_id' => $member->id,
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task + ['created_by' => $admin->id]);
        }
    }
}
