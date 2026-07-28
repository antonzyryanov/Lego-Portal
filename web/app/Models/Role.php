<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug'])]
class Role extends Model
{
    public const ADMIN = 'admin';

    public const MODERATOR = 'moderator';

    public const USER = 'user';

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
