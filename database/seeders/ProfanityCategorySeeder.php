<?php

namespace Database\Seeders;

use App\Models\ProfanityCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProfanityCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'profanity_category_code' => 'slang',
                'profanity_category_name' => 'Slang',
            ],
            [
                'profanity_category_code' => 'sexual',
                'profanity_category_name' => 'Sexual',
            ],
            [
                'profanity_category_code' => 'drugs',
                'profanity_category_name' => 'Recreational Drugs',
            ],
            [
                'profanity_category_code' => 'weapons',
                'profanity_category_name' => 'Weapons and Ammunition',
            ],
            [
                'profanity_category_code' => 'political_and_religious',
                'profanity_category_name' => 'Political and Religious',
            ],
            [
                'profanity_category_code' => 'violence',
                'profanity_category_name' => 'Violence',
            ],
            [
                'profanity_category_code' => 'alcohol',
                'profanity_category_name' => 'Alcohol',
            ],
            [
                'profanity_category_code' => 'gambling',
                'profanity_category_name' => 'Gambling',
            ]
        ];

        foreach ($data as $datum) {
            ProfanityCategory::query()->create([
                'profanity_category_code' => $datum['profanity_category_code'],
                'profanity_category_name' => $datum['profanity_category_name'],
            ]);
        }
    }
}
