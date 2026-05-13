<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Camera;
use App\Models\User;

class CameraSeeder extends Seeder
{
    public function run(): void
    {
        $c1 = Camera::create(['name' => 'Gate A Camera', 'location' => 'Main Entrance Gate A', 'stream_url' => 'https://www.w3schools.com/html/mov_bbb.mp4', 'status' => 'active']);
        $c2 = Camera::create(['name' => 'Lobby Camera', 'location' => 'Main Lobby', 'stream_url' => 'https://www.w3schools.com/html/movie.mp4', 'status' => 'active']);
        $c3 = Camera::create(['name' => 'Parking Lot Camera', 'location' => 'Parking Area B', 'stream_url' => 'https://www.w3schools.com/html/mov_bbb.mp4', 'status' => 'active']);
        $c4 = Camera::create(['name' => 'Worksite Camera 1', 'location' => 'Construction Zone A', 'stream_url' => 'https://www.w3schools.com/html/movie.mp4', 'status' => 'active']);
        $c5 = Camera::create(['name' => 'Crowd Zone Camera', 'location' => 'Public Gathering Area', 'stream_url' => 'https://www.w3schools.com/html/mov_bbb.mp4', 'status' => 'active']);
        $c6 = Camera::create(['name' => 'Gate B Camera', 'location' => 'Side Entrance Gate B', 'stream_url' => 'https://www.w3schools.com/html/movie.mp4', 'status' => 'active']);
        $c7 = Camera::create(['name' => 'Worksite Camera 2', 'location' => 'Construction Zone B', 'stream_url' => 'https://www.w3schools.com/html/mov_bbb.mp4', 'status' => 'active']);
        $c8 = Camera::create(['name' => 'Floor 2 Camera', 'location' => 'Second Floor Corridor', 'stream_url' => 'https://www.w3schools.com/html/movie.mp4', 'status' => 'active']);

        $crowdManager = User::where('name', 'Crowd Manager')->first();
        $crimeManager = User::where('name', 'Crime Manager')->first();
        $worksiteManager = User::where('name', 'Worksite Manager')->first();

        if ($crowdManager) $crowdManager->cameras()->attach([$c5->id, $c6->id]);
        if ($crimeManager) $crimeManager->cameras()->attach([$c1->id, $c2->id, $c3->id]);
        if ($worksiteManager) $worksiteManager->cameras()->attach([$c4->id, $c7->id, $c8->id]);
    }
}
