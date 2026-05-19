<?php

use App\Models\Camera;

Camera::where('name', 'Lobby Camera')->update([
    'stream_url' => 'https://www.shutterstock.com/shutterstock/videos/1032890693/preview/stock-footage-surveillance-camera-in-the-hall-of-a-modern-office-building.mp4'
]);

echo "Lobby Camera updated.\n";
