<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        if (Topic::count() === 0) {
            Topic::create([
                'name' => 'In Season',
                'slug' => 'in-season',
            ]);

            Topic::create([
                'name' => 'Food Biodiversity',
                'slug' => 'food-biodiversity',
            ]);

            Topic::create([
                'name' => 'Heritage Traditions',
                'slug' => 'heritage-traditions',
            ]);

            Topic::create([
                'name' => 'Lokalpedia Archive',
                'slug' => 'lokalpedia-archive',
            ]);

            Topic::create([
                'name' => 'Events & Talks',
                'slug' => 'events-talks',
            ]);
        }
    }
}
