<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create System Roles
        $adminRole = Role::create([
            'name' => 'admin', 
            'description' => 'System Administrator'
        ]);
        
        $teacherRole = Role::create([
            'name' => 'teacher', 
            'description' => 'Faculty / Instructor'
        ]);
        
        $studentRole = Role::create([
            'name' => 'student', 
            'description' => 'Enrolled Student'
        ]);

        // 2. Create Default System Admin
        User::create([
            'role_id' => $adminRole->id,
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'email' => 'admin@siatrack.edu.ph',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
    }
}