<?php
$shift = \App\Models\Shift::where('name', 'Day Shift')->first();
if ($shift) {
    $shift->end_time = '20:00:00';
    $shift->save();
    echo "Day Shift end_time updated successfully.\n";
} else {
    echo "Day Shift not found.\n";
}
