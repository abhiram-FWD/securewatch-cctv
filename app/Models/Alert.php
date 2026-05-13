<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Alert
 * @package App\Models
 * 
 * @property int $id
 * @property int $camera_id
 * @property int $raised_by
 * @property string $type
 * @property string $description
 * @property string|null $instruction
 * @property string|null $resolution_note
 * @property string $status
 * @property int|null $resolved_by
 * @property bool $is_emergency
 */
class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'camera_id',
        'raised_by',
        'type',
        'description',
        'instruction',
        'resolution_note',
        'status',
        'resolved_by',
        'is_emergency',
    ];

    protected $casts = [
        'is_emergency' => 'boolean',
    ];

    /**
     * The camera where this alert was triggered.
     */
    public function camera(): BelongsTo
    {
        return $this->belongsTo(Camera::class);
    }

    /**
     * The user who raised this alert.
     */
    public function raisedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'raised_by');
    }

    /**
     * The user who resolved this alert.
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Scope a query to only include open alerts.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope a query to only include emergency alerts.
     */
    public function scopeEmergency(Builder $query): Builder
    {
        return $query->where('is_emergency', true);
    }

    /**
     * Check if the alert is resolved.
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }
}
