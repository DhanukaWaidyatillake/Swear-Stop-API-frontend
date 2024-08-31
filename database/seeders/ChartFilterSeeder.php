<?php

namespace Database\Seeders;

use App\Enums\ChartFilters;
use App\Models\ChartFilter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChartFilterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (ChartFilters::cases() as $datum) {
            ChartFilter::query()->create([
                'display_name' => $datum->name,
                'code' => $datum->value,
            ]);
        }
    }
}
