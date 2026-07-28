<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::query()->where('slug', Role::ADMIN)->firstOrFail();
        $moderatorRole = Role::query()->where('slug', Role::MODERATOR)->firstOrFail();

        User::query()->updateOrCreate(
            ['email' => 'admin@lego.local'],
            [
                'name' => 'Admin',
                'password' => 'password',
                'role_id' => $adminRole->id,
                'rating' => 0,
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'moderator@lego.local'],
            [
                'name' => 'Moderator',
                'password' => 'password',
                'role_id' => $moderatorRole->id,
                'rating' => 0,
                'email_verified_at' => now(),
            ],
        );
    }
}
