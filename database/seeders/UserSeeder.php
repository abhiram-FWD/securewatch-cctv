<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'manager']);
        Role::firstOrCreate(['name' => 'guard']);

        $admin = User::create([
            'name' => 'Admin User', 'email' => 'admin@gmail.com', 'password' => Hash::make('admin@123'),
            'role' => 'admin', 'shift' => null, 'area' => null, 'status' => 'active'
        ]);
        $admin->assignRole('admin');

        $manager1 = User::create([
            'name' => 'Crowd Manager', 'email' => 'crowd@securewatch.com', 'password' => Hash::make('password123'),
            'role' => 'manager', 'shift' => 'day', 'area' => 'Crowd Control Zone', 'status' => 'active'
        ]);
        $manager1->assignRole('manager');

        $manager2 = User::create([
            'name' => 'Crime Manager', 'email' => 'crime@securewatch.com', 'password' => Hash::make('password123'),
            'role' => 'manager', 'shift' => 'day', 'area' => 'Crime Prevention Zone', 'status' => 'active'
        ]);
        $manager2->assignRole('manager');

        $manager3 = User::create([
            'name' => 'Worksite Manager', 'email' => 'worksite@securewatch.com', 'password' => Hash::make('password123'),
            'role' => 'manager', 'shift' => 'day', 'area' => 'Worksite Zone A', 'status' => 'active'
        ]);
        $manager3->assignRole('manager');

        $guard1 = User::create([
            'name' => 'Guard Alpha', 'email' => 'guard.alpha@securewatch.com', 'password' => Hash::make('password123'),
            'role' => 'guard', 'shift' => 'morning', 'area' => 'Gate A', 'status' => 'active'
        ]);
        $guard1->assignRole('guard');

        $guard2 = User::create([
            'name' => 'Guard Beta', 'email' => 'guard.beta@securewatch.com', 'password' => Hash::make('password123'),
            'role' => 'guard', 'shift' => 'morning', 'area' => 'Gate B', 'status' => 'active'
        ]);
        $guard2->assignRole('guard');

        $guard3 = User::create([
            'name' => 'Guard Charlie', 'email' => 'guard.charlie@securewatch.com', 'password' => Hash::make('password123'),
            'role' => 'guard', 'shift' => 'night', 'area' => 'Parking Lot', 'status' => 'active'
        ]);
        $guard3->assignRole('guard');

        $guard4 = User::create([
            'name' => 'Guard Delta', 'email' => 'guard.delta@securewatch.com', 'password' => Hash::make('password123'),
            'role' => 'guard', 'shift' => 'night', 'area' => 'Lobby', 'status' => 'active'
        ]);
        $guard4->assignRole('guard');
    }
}
