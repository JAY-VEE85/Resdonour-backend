<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admin
        User::create([
            'fname' => 'User',
            'lname' => 'Admin',
            'email' => 'admin@gmail.com',
            'phone_number' => '1234567881',
            'city' => 'New York',
            'barangay' => 'Manhattan',
            'role' => 'admin',
            'password' => Hash::make('Admin123'),
        ]);

        // agri admin
        User::create([
            'fname' => 'Agriculural',
            'lname' => 'Admin',
            'email' => 'agri@gmail.com',
            'phone_number' => '1234591827',
            'city' => 'New York',
            'barangay' => 'Manhattan',
            'role' => 'agri',
            'password' => Hash::make('Agriad123'),
        ]);

        // Create five users
        User::create([
            'fname' => 'John',
            'lname' => 'Doe',
            'email' => 'john@example.com',
            'phone_number' => '1234567890',
            'city' => 'New York',
            'barangay' => 'Manhattan',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'fname' => 'Jane',
            'lname' => 'Doe',
            'email' => 'jane@example.com',
            'phone_number' => '0987654321',
            'city' => 'Los Angeles',
            'barangay' => 'Hollywood',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'fname' => 'Alice',
            'lname' => 'Smith',
            'email' => 'alice@example.com',
            'phone_number' => '1112223333',
            'city' => 'Chicago',
            'barangay' => 'Downtown',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'fname' => 'Bob',
            'lname' => 'Johnson',
            'email' => 'bob@example.com',
            'phone_number' => '4445556666',
            'city' => 'Houston',
            'barangay' => 'Midtown',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'fname' => 'Charlie',
            'lname' => 'Brown',
            'email' => 'charlie@example.com',
            'phone_number' => '7778889999',
            'city' => 'San Francisco',
            'barangay' => 'Mission District',
            'role' => 'user',
            'password' => Hash::make('password123'),
        ]);
    }
}
