<?php

namespace Database\Seeders;

use App\Models\PricingTier;
use Illuminate\Database\Seeder;

class PricingTiersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiers = [
            [
                'from' => 0,
                'to' => 9,
                'price_per_api_call' => 0.5
            ],
            [
                'from' => 10,
                'to' => 99,
                'price_per_api_call' => 0.1
            ],
            [
                'from' => 100,
                'to' => 999,
                'price_per_api_call' => 0.05
            ],
            [
                'from' => 1000,
                'to' => null,
                'price_per_api_call' => 0.01
            ],
        ];

        foreach ($tiers as $tier) {
            $pricing_tier = new PricingTier();
            $pricing_tier->from = $tier['from'];
            $pricing_tier->to = $tier['to'];
            $pricing_tier->price_per_api_call = $tier['price_per_api_call'];
            $pricing_tier->save();
        }
    }
}
