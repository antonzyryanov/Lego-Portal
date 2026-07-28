<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\NewsImage;
use App\Models\User;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::query()->where('email', 'admin@lego.local')->firstOrFail();

        $articles = [
            [
                'title' => 'Welcome to Lego Portal',
                'body' => "Lego Portal gathers classic themes in one place — browse sets by series, share news with the community, and discuss builds on the forum.\n\nStart with Harry Potter, Star Wars, Indiana Jones, and Batman catalogs, then join the conversation.",
                'published_at' => now()->subDays(7),
                'images' => [
                    'https://cdn.rebrickable.com/media/sets/4842-1.jpg',
                ],
            ],
            [
                'title' => 'Spotlight: Ultimate Collector Falcon',
                'body' => "Set 10179 Ultimate Collector's Millennium Falcon remains one of the most iconic display pieces ever released.\n\nCheck the Star Wars series page for pricing history notes and related UCS-era models from 1999–2008.",
                'published_at' => now()->subDays(3),
                'images' => [
                    'https://cdn.rebrickable.com/media/sets/10179-1.jpg',
                ],
            ],
            [
                'title' => 'Forum tips for new builders',
                'body' => "When you create a forum topic you earn +5 rating points, and each reply adds +1.\n\nBe respectful, stay on theme, and help fellow fans identify rare parts and set variants.",
                'published_at' => now()->subDay(),
                'images' => [],
            ],
        ];

        foreach ($articles as $article) {
            $news = News::query()->updateOrCreate(
                [
                    'author_id' => $author->id,
                    'title' => $article['title'],
                ],
                [
                    'body' => $article['body'],
                    'published_at' => $article['published_at'],
                ],
            );

            $news->images()->delete();

            foreach ($article['images'] as $index => $path) {
                NewsImage::query()->create([
                    'news_id' => $news->id,
                    'path' => $path,
                    'sort_order' => $index,
                ]);
            }
        }
    }
}
