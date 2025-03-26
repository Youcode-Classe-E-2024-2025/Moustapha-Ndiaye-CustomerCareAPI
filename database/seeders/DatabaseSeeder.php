<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\Response;
use App\Models\Priority;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create agent users
        User::create([
            'name' => 'Agent User',
            'email' => 'agent@example.com',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'department' => 'Support',
        ]);

        // Create client user
        User::create([
            'name' => 'Client User',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
        ]);

        // Create statuses
        $statuses = [
            [
                'name' => 'New',
                'description' => 'A new ticket that has not been assigned yet',
                'color' => '#3498db',
                'order' => 1,
                'is_default' => true,
            ],
            [
                'name' => 'Open',
                'description' => 'Ticket has been assigned but work has not started',
                'color' => '#2ecc71',
                'order' => 2,
                'is_default' => false,
            ],
            [
                'name' => 'In Progress',
                'description' => 'Agent is actively working on the ticket',
                'color' => '#f39c12',
                'order' => 3,
                'is_default' => false,
            ],
            [
                'name' => 'Resolved',
                'description' => 'Solution has been provided, awaiting client confirmation',
                'color' => '#27ae60',
                'order' => 4,
                'is_default' => false,
            ],
            [
                'name' => 'Closed',
                'description' => 'Ticket has been resolved and confirmed by client',
                'color' => '#7f8c8d',
                'order' => 5,
                'is_default' => false,
            ],
        ];

        foreach ($statuses as $status) {
            Status::create($status);
        }

        // Create priorities
        $priorities = [
            ['name' => 'High', 'color' => '#e74c3c'],
            ['name' => 'Medium', 'color' => '#f39c12'],
            ['name' => 'Low', 'color' => '#7f8c8d'],
        ];

        foreach ($priorities as $priority) {
            Priority::create($priority);
        }

        // Create categories
        $categories = [
            ['name' => 'Bug'],
            ['name' => 'Feature Request'],
            ['name' => 'Authentication'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create sample tickets and responses
        $clientId = 3; // Client user ID
        $agentId = 2;  // Agent user ID
        $newStatusId = 1;
        $openStatusId = 2;
        $inProgressStatusId = 3;

        // Ticket 1
        $ticket1 = Ticket::create([
            'title' => 'Cannot login to my account',
            'description' => 'I am trying to login but keep getting an error message saying "Invalid credentials".',
            'creator_id' => $clientId,
            'assigned_to' => $agentId,
            'status_id' => $inProgressStatusId,
            'priority' => 'high',
            'category' => 'Authentication',
        ]);

        Response::create([
            'ticket_id' => $ticket1->id,
            'user_id' => $agentId,
            'content' => 'I am looking into this issue. Could you please provide your username and when you last successfully logged in?',
            'is_internal' => false,
        ]);

        Response::create([
            'ticket_id' => $ticket1->id,
            'user_id' => $clientId,
            'content' => 'My username is client1. I was able to log in yesterday but today it stopped working.',
            'is_internal' => false,
        ]);

        // Ticket 2
        Ticket::create([
            'title' => 'Feature request: dark mode',
            'description' => 'It would be great if you could add a dark mode to the application. It would help reduce eye strain when using the app at night.',
            'creator_id' => $clientId,
            'status_id' => $newStatusId,
            'priority' => 'low',
            'category' => 'Feature Request',
        ]);

        // Ticket 3
        $ticket3 = Ticket::create([
            'title' => 'Error when submitting form',
            'description' => 'I get a 500 error when trying to submit the contact form on your website.',
            'creator_id' => $clientId,
            'assigned_to' => $agentId,
            'status_id' => $openStatusId,
            'priority' => 'medium',
            'category' => 'Bug',
        ]);

        Response::create([
            'ticket_id' => $ticket3->id,
            'user_id' => $agentId,
            'content' => 'Thanks for reporting this. I will check the server logs and get back to you.',
            'is_internal' => false,
        ]);

        Response::create([
            'ticket_id' => $ticket3->id,
            'user_id' => $agentId,
            'content' => 'Need to check with the dev team about recent changes to the form validation.',
            'is_internal' => true,
        ]);
    }
}
