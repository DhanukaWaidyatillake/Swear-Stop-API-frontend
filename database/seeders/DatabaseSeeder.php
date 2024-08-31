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
        (new ProfanityCategorySeeder())->run();
        (new ProfanityDatasetSeeder())->run();
        (new PricingTiersSeeder())->run();
        (new ConfigsSeeder())->run();
        (new ChartFilterSeeder())->run();
    }
}
