<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Message;
use App\Models\User;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('name', 'Admin User')->first();
        $guardAlpha = User::where('name', 'Guard Alpha')->first();

        Message::create([
            'from_user_id' => $admin->id, 'to_user_id' => null, 'to_role' => 'guard', 'type' => 'instruction',
            'message' => 'All guards please ensure extra vigilance during evening hours.', 'is_read' => false
        ]);

        Message::create([
            'from_user_id' => $admin->id, 'to_user_id' => $guardAlpha->id, 'to_role' => null, 'type' => 'warning',
            'message' => 'You were slow to respond to alert #004. Please be more alert.', 'is_read' => false
        ]);

        Message::create([
            'from_user_id' => $admin->id, 'to_user_id' => null, 'to_role' => 'all', 'type' => 'general',
            'message' => 'System maintenance scheduled for Sunday 2AM. Please be prepared.', 'is_read' => false
        ]);
    }
}
