<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Camera
 * @package App\Models
 * 
 * @property int $id
 * @property string $name
 * @property string $location
 * @property string $stream_url
 * @property string $status
 */
class Camera extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'stream_url',
        'status',
    ];

    /**
     * Alerts associated with this camera.
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * Users (managers) assigned to this camera.
     */
    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'camera_manager', 'camera_id', 'manager_id')->withTimestamps();
    }

    /**
     * Scope a query to only include active cameras.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }
}
