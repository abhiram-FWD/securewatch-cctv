<?php

use App\Models\Camera;

$updates = [
    'Gate A Camera' => 'https://www.shutterstock.com/shutterstock/videos/4006050861/preview/stock-footage-male-porter-falls-hard-on-ice-while-unloading-parcels-from-van-and-cannot-stand-anxious-woman.mp4',
    'Parking Lot Camera' => 'https://www.shutterstock.com/shutterstock/videos/1043536546/preview/stock-footage-sochi-russia-parking-at-the-shopping-center-the-movement-of-vehicles-time-laps.mp4',
    'Worksite Camera 1' => 'https://www.shutterstock.com/shutterstock/videos/1022093263/preview/stock-footage-cctv-view-of-construction-site.mp4',
    'Worksite Camera 2' => 'https://www.shutterstock.com/shutterstock/videos/12820913/preview/stock-footage-cctv-construction-site-in-hong-hong-timelapse.mp4',
    'Crowd Zone Camera' => 'https://www.shutterstock.com/shutterstock/videos/1020030826/preview/stock-footage-high-view-of-commuters-walking-facial-recognition-interface-showing-personal-data-for-each-person.mp4',
    'Gate B Camera' => 'https://media.istockphoto.com/id/827662162/video/inside-a-warehouse.mp4?s=mp4-640x640-is&k=20&c=RxIzw6EuFjf5metSpwo2V-CoIoJrLD8n1rWKv-FciJU=',
    'Floor 2 Camera' => 'https://www.shutterstock.com/shutterstock/videos/1088236923/preview/stock-footage-burglar-or-thief-breaks-down-a-door-in-a-house-or-apartment-and-enters-surveillance-camera-type.mp4'
];

foreach ($updates as $name => $url) {
    Camera::where('name', $name)->update(['stream_url' => $url]);
    echo "Updated $name\n";
}
