<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@cskm.com'],
            [
                'name' => 'CSKM Admin',
                'role' => Role::Admin->value,
                'password' => 'password123',
                'email_verified_at' => now(),
            ]
        );
    }
}
