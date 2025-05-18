<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        Category::create([
            'name' => 'Ingredient Spotlights',
            'slug' => 'ingredient-spotlights',
        ]);

        Category::create([
            'name' => 'Cultural Heritage Features',
            'slug' => 'cultural-heritage-features',
        ]);

        Category::create([
            'name' => 'Market/Travel Explorations',
            'slug' => 'market-travel-explorations',
        ]);

        Category::create([
            'name' => 'Events & Outreach',
            'slug' => 'events-outreach',
        ]);

        Category::create([
            'name' => 'Interviews & Collaborations',
            'slug' => 'interviews-collaborations',
        ]);
    }
}
