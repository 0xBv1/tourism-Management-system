<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed.hassan@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+201234567890',
                'nationality' => 'Egyptian',
                'birthdate' => '1985-03-15',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+44123456789',
                'nationality' => 'British',
                'birthdate' => '1990-07-22',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Mohammed Ali',
                'email' => 'mohammed.ali@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+201234567891',
                'nationality' => 'Egyptian',
                'birthdate' => '1988-11-08',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Emma Wilson',
                'email' => 'emma.wilson@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+33123456789',
                'nationality' => 'French',
                'birthdate' => '1992-04-12',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Fatima Zahra',
                'email' => 'fatima.zahra@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+201234567892',
                'nationality' => 'Moroccan',
                'birthdate' => '1987-09-30',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'John Smith',
                'email' => 'john.smith@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+11234567890',
                'nationality' => 'American',
                'birthdate' => '1983-12-05',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Aisha Rahman',
                'email' => 'aisha.rahman@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+201234567893',
                'nationality' => 'Pakistani',
                'birthdate' => '1991-06-18',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Carlos Rodriguez',
                'email' => 'carlos.rodriguez@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+34123456789',
                'nationality' => 'Spanish',
                'birthdate' => '1986-01-25',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Nour El Din',
                'email' => 'nour.eldin@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+201234567894',
                'nationality' => 'Egyptian',
                'birthdate' => '1984-08-14',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+34123456787',
                'nationality' => 'Spanish',
                'birthdate' => '1989-05-20',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Omar Khalil',
                'email' => 'omar.khalil@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+201234567895',
                'nationality' => 'Egyptian',
                'birthdate' => '1987-12-03',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Lisa Chen',
                'email' => 'lisa.chen@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+86123456789',
                'nationality' => 'Chinese',
                'birthdate' => '1993-02-28',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Youssef Ibrahim',
                'email' => 'youssef.ibrahim@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+201234567896',
                'nationality' => 'Egyptian',
                'birthdate' => '1986-10-17',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Anna Kowalski',
                'email' => 'anna.kowalski@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+48123456789',
                'nationality' => 'Polish',
                'birthdate' => '1988-07-11',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Hassan Mahmoud',
                'email' => 'hassan.mahmoud@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+201234567897',
                'nationality' => 'Egyptian',
                'birthdate' => '1986-04-09',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Sophie Martin',
                'email' => 'sophie.martin@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+33123456788',
                'nationality' => 'French',
                'birthdate' => '1991-11-26',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Karim Abdel',
                'email' => 'karim.abdel@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+201234567898',
                'nationality' => 'Egyptian',
                'birthdate' => '1989-01-31',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Isabella Silva',
                'email' => 'isabella.silva@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+55123456789',
                'nationality' => 'Brazilian',
                'birthdate' => '1992-08-07',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Tarek Mansour',
                'email' => 'tarek.mansour@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+201234567899',
                'nationality' => 'Egyptian',
                'birthdate' => '1985-06-13',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Elena Popov',
                'email' => 'elena.popov@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+79123456789',
                'nationality' => 'Russian',
                'birthdate' => '1988-03-24',
                'blocked' => false,
                'image' => null,
            ],
            [
                'name' => 'Blocked Client Example',
                'email' => 'blocked.client@email.com',
                'password' => Hash::make('password123'),
                'phone' => '+201234567800',
                'nationality' => 'Egyptian',
                'birthdate' => '1990-01-01',
                'blocked' => true,
                'image' => null,
            ],
        ];

        foreach ($clients as $clientData) {
            // Check if client already exists
            $existingClient = Client::where('email', $clientData['email'])->first();
            
            if ($existingClient) {
                // Client already exists, skip creation
                continue;
            }
            
            Client::create($clientData);
        }

        $this->command->info('Clients seeded successfully!');
    }
}

