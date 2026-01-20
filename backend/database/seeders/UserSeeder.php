<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'company_name' => 'Travel Platform',
            'phone' => '+1-555-0100',
        ]);

        // Staff User
        User::create([
            'name' => 'Staff Member',
            'email' => 'staff@example.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'is_active' => true,
            'company_name' => 'Travel Platform',
            'phone' => '+1-555-0101',
        ]);

        // Agent Users
        User::create([
            'name' => 'John Agent',
            'email' => 'agent1@example.com',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'is_active' => true,
            'company_name' => 'Adventure Tours Inc',
            'phone' => '+1-555-0102',
        ]);

        User::create([
            'name' => 'Sarah Agent',
            'email' => 'agent2@example.com',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'is_active' => true,
            'company_name' => 'Dream Vacations',
            'phone' => '+1-555-0103',
        ]);

        User::create([
            'name' => 'Mike Agent',
            'email' => 'agent3@example.com',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'is_active' => true,
            'company_name' => 'Global Travels',
            'phone' => '+1-555-0104',
        ]);

        // Hotel Partner Users
        User::create([
            'name' => 'Hotel Manager 1',
            'email' => 'hotel1@example.com',
            'password' => Hash::make('password'),
            'role' => 'hotel_partner',
            'is_active' => true,
            'company_name' => 'Luxury Hotels Group',
            'phone' => '+1-555-0105',
        ]);

        User::create([
            'name' => 'Hotel Manager 2',
            'email' => 'hotel2@example.com',
            'password' => Hash::make('password'),
            'role' => 'hotel_partner',
            'is_active' => true,
            'company_name' => 'Budget Hotels LLC',
            'phone' => '+1-555-0106',
        ]);

        User::create([
            'name' => 'Hotel Manager 3',
            'email' => 'hotel3@example.com',
            'password' => Hash::make('password'),
            'role' => 'hotel_partner',
            'is_active' => true,
            'company_name' => 'Resort Paradise',
            'phone' => '+1-555-0107',
        ]);

        // Operator Users
        User::create([
            'name' => 'Field Operator 1',
            'email' => 'operator1@example.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'is_active' => true,
            'company_name' => 'On-Ground Services',
            'phone' => '+1-555-0108',
        ]);

        User::create([
            'name' => 'Field Operator 2',
            'email' => 'operator2@example.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'is_active' => true,
            'company_name' => 'On-Ground Services',
            'phone' => '+1-555-0109',
        ]);

        User::create([
            'name' => 'Field Operator 3',
            'email' => 'operator3@example.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'is_active' => true,
            'company_name' => 'On-Ground Services',
            'phone' => '+1-555-0110',
        ]);
    }
}
