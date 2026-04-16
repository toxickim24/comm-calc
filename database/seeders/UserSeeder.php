<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@baysidepavers.com',
            'password' => 'password',
            'role' => 'admin',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        User::create([
            'name' => 'Sales Manager',
            'email' => 'manager@baysidepavers.com',
            'password' => 'password',
            'role' => 'manager',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        User::create([
            'name' => 'John Smith',
            'email' => 'john@baysidepavers.com',
            'password' => 'password',
            'role' => 'sales_rep',
            'is_active' => true,
            'force_password_change' => false,
        ]);

        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@baysidepavers.com',
            'password' => 'password',
            'role' => 'sales_rep',
            'is_active' => true,
            'force_password_change' => false,
        ]);
    }
}
