<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        if (Tag::count() === 0) {
            Tag::create([
                'name' => 'Sustainable Farming',
                'slug' => 'sustainable-farming',
            ]);

            Tag::create([
                'name' => 'Culinary Heritage',
                'slug' => 'culinary-heritage',
            ]);

            Tag::create([
                'name' => 'Endemic',
                'slug' => 'endemic',
            ]);
        }
    }
}
