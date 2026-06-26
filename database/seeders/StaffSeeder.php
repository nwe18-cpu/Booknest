<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = \App\Models\Role::firstOrCreate(['name' => 'admin']);
        $staffRole = \App\Models\Role::firstOrCreate(['name' => 'staff']);

        // 1. Seed Admin
        \App\Models\Staff::updateOrCreate([
            'email' => 'admin@booknest.com',
        ], [
            'role_id' => $adminRole->id,
            'name' => 'System Administrator',
            'phone' => '09999999999',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'status' => 'active',
        ]);

        // 2. Seed Staff
        \App\Models\Staff::updateOrCreate([
            'email' => 'staff@booknest.com',
        ], [
            'role_id' => $staffRole->id,
            'name' => 'Staff Member',
            'phone' => '09123456789',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'status' => 'active',
        ]);


        
        // Seed initial authors for convenience
        \App\Models\Author::firstOrCreate(['name' => 'Matt Haig'], ['image' => null]);
        \App\Models\Author::firstOrCreate(['name' => 'James Clear'], ['image' => null]);
        \App\Models\Author::firstOrCreate(['name' => 'Robert C. Martin'], ['image' => null]);
        \App\Models\Author::firstOrCreate(['name' => 'Arthur Conan Doyle'], ['image' => null]);
    }
}
