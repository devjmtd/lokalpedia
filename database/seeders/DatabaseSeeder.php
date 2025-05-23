<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        if (!User::where('email', 'admin@lokalpedia.com')->exists()) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@lokalpedia.com',
            ]);
        }

        $this->call([
            CategoriesSeeder::class,
            TagSeeder::class,
            TopicSeeder::class,
        ]);
    }
}
