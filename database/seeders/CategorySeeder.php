<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the default material categories as specified in the requirements.
     *
     * These are the master values for the category dropdown.
     * Uses updateOrCreate so the seeder is safe to run multiple times.
     */
    public function run(): void
    {
        $categories = [
            'Raw Material',
            'Finish Goods',
            'Spares',
            'Machines',
            'Others',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['name' => $name],  // match condition
                ['name' => $name]   // data to set (no-op if already exists)
            );
        }

        $this->command->info('CategorySeeder: ' . count($categories) . ' categories seeded.');
    }
}
