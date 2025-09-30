<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DefaultUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
        
            [
                'name' => 'Sales Manager',
                'email' => 'sales@perfect.com',
                'password' => Hash::make('password123'),
                'roles' => ['Sales']
            ],
            [
                'name' => 'Reservation Manager',
                'email' => 'reservation@perfect.com',
                'password' => Hash::make('password123'),
                'roles' => ['Reservation']
            ],
            [
                'name' => 'Operation Manager',
                'email' => 'operation@perfect.com',
                'password' => Hash::make('password123'),
                'roles' => ['Operator']
            ],
            [
                'name' => 'Finance Manager',
                'email' => 'finance@perfect.com',
                'password' => Hash::make('password123'),
                'roles' => ['Finance']
            ],
            [
                'name' => 'Admin Manager',
                'email' => 'admin@prefect.com',
                'password' => Hash::make('password123'),
                'roles' => ['Admin']
            ]
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => $userData['password'],
                    'email_verified_at' => now(),
                ]
            );

            // Assign roles
            foreach ($userData['roles'] as $roleName) {
                if ($role = Role::where('name', $roleName)->first()) {
                    $user->assignRole($role);
                }
            }

            $this->command->info("Created user: {$user->name} ({$user->email}) with roles: " . implode(', ', $userData['roles']));
        }
    }
}
