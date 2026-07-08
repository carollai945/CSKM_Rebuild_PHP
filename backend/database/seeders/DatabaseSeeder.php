<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (in_array(config('app.env'), ['local', 'development'], true)) {
            $this->call(DevelopmentSeeder::class);
        }
    }
}
