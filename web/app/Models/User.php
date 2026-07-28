<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role_id', 'avatar', 'rating', 'banned_until'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
            'banned_until' => 'datetime',
            'rating' => 'integer',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class, 'author_id');
    }

    public function forumTopics(): HasMany
    {
        return $this->hasMany(ForumTopic::class);
    }

    public function forumMessages(): HasMany
    {
        return $this->hasMany(ForumMessage::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->slug === Role::ADMIN;
    }

    public function isModerator(): bool
    {
        return $this->role?->slug === Role::MODERATOR;
    }

    public function isBanned(): bool
    {
        return $this->banned_until !== null && $this->banned_until->isFuture();
    }

    public function canModerateForum(): bool
    {
        return $this->isAdmin() || $this->isModerator();
    }

    public function canManageContent(): bool
    {
        return $this->isAdmin();
    }

    public function canCreateContent(): bool
    {
        return $this->isAdmin() || $this->isModerator();
    }

    public function incrementRating(int $amount = 1): void
    {
        $this->increment('rating', $amount);
    }
}
