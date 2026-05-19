<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        Shift::create(['name' => 'Morning Shift', 'start_time' => '06:00:00', 'end_time' => '18:00:00']);
        Shift::create(['name' => 'Night Shift', 'start_time' => '18:00:00', 'end_time' => '06:00:00']);
        Shift::create(['name' => 'Day Shift', 'start_time' => '09:00:00', 'end_time' => '20:00:00']);
    }
}
