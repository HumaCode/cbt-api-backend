<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Spatie\Permission\Traits\HasRoles;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'username',
    'email',
    'password',
    'telp',
    'gender',
    'is_active',
    'last_activity',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements JWTSubject, HasMedia
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUlids, HasRoles, InteractsWithMedia;

    protected $guard_name = 'api';

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
            'last_activity' => 'datetime',
        ];
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Relationship with Groups.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id');
    }

    /**
     * Relationship with Assessment Sessions.
     */
    public function assessmentSessions(): HasMany
    {
        return $this->hasMany(AssessmentSession::class);
    }

    /**
     * Get the user's avatar URL from Spatie Media Library.
     *
     * @return string|null
     */
    public function getAvatarAttribute(): ?string
    {
        return $this->getFirstMediaUrl('avatar') ?: null;
    }
}

