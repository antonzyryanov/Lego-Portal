<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'year_from', 'year_to', 'description'])]
class Series extends Model
{
    protected $table = 'series';

    protected function casts(): array
    {
        return [
            'year_from' => 'integer',
            'year_to' => 'integer',
        ];
    }

    public function legoSets(): HasMany
    {
        return $this->hasMany(LegoSet::class);
    }
}
