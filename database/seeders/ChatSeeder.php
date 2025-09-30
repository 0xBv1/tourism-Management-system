<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create roles if they don't exist
        $salesRole = Role::firstOrCreate(['name' => 'Sales', 'guard_name' => 'web']);
        $reservationRole = Role::firstOrCreate(['name' => 'Reservation', 'guard_name' => 'web']);

        // Get or create users with specific roles
        $salesUser = User::firstOrCreate(
            ['email' => 'sales@example.com'],
            [
                'name' => 'John Sales',
                'password' => bcrypt('password'),
                'position' => 'Sales Manager'
            ]
        );

        $reservationUser = User::firstOrCreate(
            ['email' => 'reservation@example.com'],
            [
                'name' => 'Jane Reservation',
                'password' => bcrypt('password'),
                'position' => 'Reservation Specialist'
            ]
        );

        // Assign roles if they don't exist
        if (!$salesUser->hasRole('Sales')) {
            $salesUser->assignRole('Sales');
        }

        if (!$reservationUser->hasRole('Reservation')) {
            $reservationUser->assignRole('Reservation');
        }

        // Get the first inquiry or create one
        $inquiry = Inquiry::first();
        if (!$inquiry) {
            $inquiry = Inquiry::create([
                'guest_name' => 'Sample Client',
                'email' => 'client@example.com',
                'phone' => '+1234567890',
                'subject' => 'Sample Inquiry for Chat Demo',
                'message' => 'This is a sample inquiry to demonstrate the chat functionality.',
                'status' => 'pending',
                'assigned_to' => $salesUser->id,
            ]);
        }

        // Create sample chat messages
        $messages = [
            [
                'sender_id' => $salesUser->id,
                'message' => 'Hello! I received your inquiry about the tour package. Let me check the availability for you.',
                'created_at' => now()->subHours(2),
            ],
            [
                'sender_id' => $reservationUser->id,
                'message' => 'Hi John! I can confirm that we have availability for the dates you mentioned. The package includes accommodation and guided tours.',
                'created_at' => now()->subHours(1, 45),
            ],
            [
                'sender_id' => $salesUser->id,
                'message' => 'Great! What about the pricing? Can we offer any discounts for a group booking?',
                'created_at' => now()->subHours(1, 30),
            ],
            [
                'sender_id' => $reservationUser->id,
                'message' => 'For groups of 5 or more, we can offer a 10% discount. I\'ll prepare a detailed quote for you.',
                'created_at' => now()->subHours(1, 15),
                'read_at' => now()->subHours(1, 10),
            ],
            [
                'sender_id' => $salesUser->id,
                'message' => 'Perfect! Please send the quote as soon as possible. The client is waiting for the details.',
                'created_at' => now()->subMinutes(30),
            ],
            [
                'sender_id' => $reservationUser->id,
                'message' => 'I\'ll have it ready within the next hour. I\'ll also include some additional options they might be interested in.',
                'created_at' => now()->subMinutes(15),
            ],
        ];

        foreach ($messages as $messageData) {
            Chat::create([
                'inquiry_id' => $inquiry->id,
                'sender_id' => $messageData['sender_id'],
                'message' => $messageData['message'],
                'created_at' => $messageData['created_at'],
                'read_at' => $messageData['read_at'] ?? null,
            ]);
        }

        $this->command->info('Sample chat messages created successfully!');
    }
}
