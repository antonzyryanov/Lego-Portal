<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'series_id',
    'name',
    'description',
    'original_price',
    'release_date',
    'article_number',
    'image_path',
])]
class LegoSet extends Model
{
    protected function casts(): array
    {
        return [
            'original_price' => 'decimal:2',
            'release_date' => 'date',
        ];
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }
}
