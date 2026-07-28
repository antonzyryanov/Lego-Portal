<?php

namespace Database\Seeders;

use App\Models\ForumMessage;
use App\Models\ForumTopic;
use App\Models\User;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@lego.local')->firstOrFail();
        $moderator = User::query()->where('email', 'moderator@lego.local')->firstOrFail();

        $topic = ForumTopic::query()->updateOrCreate(
            [
                'user_id' => $admin->id,
                'title' => 'Favorite Hogwarts Castle — which year wins?',
            ],
            [
                'body' => "Between 4709, 4757, 5378, and 4842, which Hogwarts Castle is your favorite and why?\n\nShare photos, part opinions, and playability notes.",
            ],
        );

        if ($topic->messages()->count() === 0) {
            ForumMessage::query()->create([
                'topic_id' => $topic->id,
                'user_id' => $moderator->id,
                'body' => '4842 for me — great room variety and minifigure selection without being too fragile on display.',
            ]);

            ForumMessage::query()->create([
                'topic_id' => $topic->id,
                'user_id' => $admin->id,
                'body' => 'I still love 5378 for the tower silhouette. Curious what everyone thinks about the older 4709 modular feel.',
            ]);

            $admin->incrementRating(5);
            $moderator->incrementRating(1);
            $admin->incrementRating(1);
        }
    }
}
