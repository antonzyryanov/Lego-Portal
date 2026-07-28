<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrator', 'slug' => Role::ADMIN],
            ['name' => 'Moderator', 'slug' => Role::MODERATOR],
            ['name' => 'User', 'slug' => Role::USER],
        ];

        foreach ($roles as $role) {
            Role::query()->updateOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name']],
            );
        }
    }
}
