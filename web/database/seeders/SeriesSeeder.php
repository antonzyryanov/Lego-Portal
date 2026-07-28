<?php

namespace Database\Seeders;

use App\Models\Series;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SeriesSeeder extends Seeder
{
    public function run(): void
    {
        $series = [
            [
                'name' => 'Harry Potter',
                'year_from' => 2001,
                'year_to' => 2011,
                'description' => 'Classic LEGO Harry Potter sets from the early film waves, featuring Hogwarts and iconic characters.',
            ],
            [
                'name' => 'Star Wars',
                'year_from' => 1999,
                'year_to' => 2008,
                'description' => 'Original LEGO Star Wars sets spanning Episode I–III eras and Ultimate Collector Series builds.',
            ],
            [
                'name' => 'Indiana Jones',
                'year_from' => 2008,
                'year_to' => 2009,
                'description' => 'Adventure-packed LEGO Indiana Jones sets based on the original trilogy and Crystal Skull.',
            ],
            [
                'name' => 'Batman',
                'year_from' => 2006,
                'year_to' => 2008,
                'description' => 'LEGO Batman sets with Bat-vehicles, Arkham Asylum, and classic Gotham villains.',
            ],
        ];

        foreach ($series as $item) {
            Series::query()->updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'name' => $item['name'],
                    'year_from' => $item['year_from'],
                    'year_to' => $item['year_to'],
                    'description' => $item['description'],
                ],
            );
        }
    }
}
