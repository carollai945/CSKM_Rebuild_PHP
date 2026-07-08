<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    public function run(): void
    {
        if (config('app.env') === 'production') {
            return;
        }

        $this->call([
            MasterDataSeeder::class,
            AdminSeeder::class,
        ]);
    }
}
