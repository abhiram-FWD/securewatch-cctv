<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class Shift
 * @package App\Models
 * 
 * @property int $id
 * @property string $name
 * @property string $start_time
 * @property string $end_time
 */
class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
    ];

    /**
     * Check if current time is between start_time and end_time.
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        // Handle shifts that span across midnight (e.g. 22:00 to 06:00)
        if ($end->lessThan($start)) {
            return $now->between($start, Carbon::parse('23:59:59')) ||
                   $now->between(Carbon::parse('00:00:00'), $end);
        }

        return $now->between($start, $end);
    }
}
