<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * @package App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int|null $team_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Team|null $team
 * @property-read Collection|Task[] $createdTasks
 * @property-read Collection|Task[] $assignedTasks
 * @property-read Collection|Comment[] $comments
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'team_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Gets the user team
     *
     * @return BelongsTo|null
     */
    public function team(): ?BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Gets the user created tasks
     *
     * @return HasMany
     */
    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'creator_user_id');
    }

    /**
     * Gets the user assigned tasks
     *
     * @return HasMany
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_user_id');
    }

    /**
     * Gets the user task comments
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'creator_user_id');
    }
}
