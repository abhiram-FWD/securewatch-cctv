<?php

use App\Models\Camera;

Camera::where('name', 'Lobby Camera')->update([
    'stream_url' => 'https://www.shutterstock.com/shutterstock/videos/3788587621/preview/stock-footage-cctv-camera-shot-female-receptionist-handing-document.mp4'
]);

echo "Lobby Camera updated with new link.\n";
