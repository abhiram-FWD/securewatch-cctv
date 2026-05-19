<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Camera;
use App\Models\User;

class CameraSeeder extends Seeder
{
    public function run(): void
    {
        $c1 = Camera::create(['name' => 'Gate A Camera', 'location' => 'Main Entrance Gate A', 'stream_url' => 'https://www.shutterstock.com/shutterstock/videos/4006050861/preview/stock-footage-male-porter-falls-hard-on-ice-while-unloading-parcels-from-van-and-cannot-stand-anxious-woman.mp4', 'status' => 'active']);
        $c2 = Camera::create(['name' => 'Lobby Camera', 'location' => 'Main Lobby', 'stream_url' => 'https://www.shutterstock.com/shutterstock/videos/3788587621/preview/stock-footage-cctv-camera-shot-female-receptionist-handing-document.mp4', 'status' => 'active']);
        $c3 = Camera::create(['name' => 'Parking Lot Camera', 'location' => 'Parking Area B', 'stream_url' => 'https://www.shutterstock.com/shutterstock/videos/1043536546/preview/stock-footage-sochi-russia-parking-at-the-shopping-center-the-movement-of-vehicles-time-laps.mp4', 'status' => 'active']);
        $c4 = Camera::create(['name' => 'Worksite Camera 1', 'location' => 'Construction Zone A', 'stream_url' => 'https://www.shutterstock.com/shutterstock/videos/1022093263/preview/stock-footage-cctv-view-of-construction-site.mp4', 'status' => 'active']);
        $c5 = Camera::create(['name' => 'Crowd Zone Camera', 'location' => 'Public Gathering Area', 'stream_url' => 'https://www.shutterstock.com/shutterstock/videos/1020030826/preview/stock-footage-high-view-of-commuters-walking-facial-recognition-interface-showing-personal-data-for-each-person.mp4', 'status' => 'active']);
        $c6 = Camera::create(['name' => 'Gate B Camera', 'location' => 'Side Entrance Gate B', 'stream_url' => 'https://media.istockphoto.com/id/827662162/video/inside-a-warehouse.mp4?s=mp4-640x640-is&k=20&c=RxIzw6EuFjf5metSpwo2V-CoIoJrLD8n1rWKv-FciJU=', 'status' => 'active']);
        $c7 = Camera::create(['name' => 'Worksite Camera 2', 'location' => 'Construction Zone B', 'stream_url' => 'https://www.shutterstock.com/shutterstock/videos/12820913/preview/stock-footage-cctv-construction-site-in-hong-hong-timelapse.mp4', 'status' => 'active']);
        $c8 = Camera::create(['name' => 'Floor 2 Camera', 'location' => 'Second Floor Corridor', 'stream_url' => 'https://www.shutterstock.com/shutterstock/videos/1088236923/preview/stock-footage-burglar-or-thief-breaks-down-a-door-in-a-house-or-apartment-and-enters-surveillance-camera-type.mp4', 'status' => 'active']);

        $crowdManager = User::where('name', 'Crowd Manager')->first();
        $crimeManager = User::where('name', 'Crime Manager')->first();
        $worksiteManager = User::where('name', 'Worksite Manager')->first();

        if ($crowdManager) $crowdManager->cameras()->attach([$c5->id, $c6->id]);
        if ($crimeManager) $crimeManager->cameras()->attach([$c1->id, $c2->id, $c3->id]);
        if ($worksiteManager) $worksiteManager->cameras()->attach([$c4->id, $c7->id, $c8->id]);
    }
}
