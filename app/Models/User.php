<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

/**
 * Class User
 * @package App\Models
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property string|null $shift
 * @property string|null $area
 * @property string $status
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'shift',
        'area',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Alerts raised by this user.
     */
    public function raisedAlerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'raised_by');
    }

    /**
     * Alerts resolved by this user.
     */
    public function resolvedAlerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'resolved_by');
    }

    /**
     * Messages sent by this user.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'from_user_id');
    }

    /**
     * Messages received by this user.
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'to_user_id');
    }

    /**
     * Notifications belonging to this user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Logs created by this user.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    /**
     * Cameras assigned to this user (manager).
     */
    public function cameras(): BelongsToMany
    {
        return $this->belongsToMany(Camera::class, 'camera_manager', 'manager_id', 'camera_id')->withTimestamps();
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a manager.
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user is a guard.
     */
    public function isGuard(): bool
    {
        return $this->role === 'guard';
    }

    /**
     * Check if user is currently on duty based on their shift.
     */
    public function isOnDuty(): bool
    {
        if (!$this->shift) {
            return false;
        }

        $shiftRecord = Shift::where('name', 'LIKE', "%{$this->shift}%")->first();
        
        if (!$shiftRecord) {
            return false;
        }

        return $shiftRecord->isActive();
    }
}
