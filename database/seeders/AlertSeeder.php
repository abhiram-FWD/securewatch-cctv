<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Alert;
use App\Models\User;

class AlertSeeder extends Seeder
{
    public function run(): void
    {
        $guardAlpha = User::where('name', 'Guard Alpha')->first();
        $guardBeta = User::where('name', 'Guard Beta')->first();
        $guardCharlie = User::where('name', 'Guard Charlie')->first();
        $guardDelta = User::where('name', 'Guard Delta')->first();
        $worksiteManager = User::where('name', 'Worksite Manager')->first();

        Alert::create([
            'camera_id' => 1, 'raised_by' => $guardAlpha->id, 'type' => 'crime',
            'description' => 'Suspicious person spotted near Gate A', 'status' => 'open', 'is_emergency' => false
        ]);

        Alert::create([
            'camera_id' => 5, 'raised_by' => $guardBeta->id, 'type' => 'crowd',
            'description' => 'Crowd density too high at gathering area', 'instruction' => 'Guard Alpha please go check immediately',
            'status' => 'open', 'is_emergency' => false
        ]);

        Alert::create([
            'camera_id' => 4, 'raised_by' => $worksiteManager->id, 'type' => 'worksite',
            'description' => 'Workers not following safety protocols', 'status' => 'open', 'is_emergency' => false
        ]);

        Alert::create([
            'camera_id' => 2, 'raised_by' => $guardCharlie->id, 'type' => 'emergency',
            'description' => 'Fight breaking out in main lobby', 'status' => 'open', 'is_emergency' => true
        ]);

        Alert::create([
            'camera_id' => 3, 'raised_by' => $guardDelta->id, 'type' => 'crime',
            'description' => 'Unattended vehicle in parking lot', 'resolution_note' => 'Vehicle owner was contacted and removed the vehicle from restricted area.',
            'status' => 'resolved', 'resolved_by' => $guardDelta->id, 'is_emergency' => false
        ]);
    }
}
