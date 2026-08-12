<?php

namespace Modules\Glitter\Gallery\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GallerySampleSeeder::class,
        ]);
    }
}
