<?php

namespace Database\Seeders;

use App\Models\SiteConfig;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class ConfigsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artisan::call('signup_secret:refresh');

        SiteConfig::query()->updateOrCreate(
            ['key' => 'subscription_renewal_date'],
            [
                'key' => 'subscription_renewal_date',
                'value' => '2200-01-01T00:00:00Z'
            ]
        );

        SiteConfig::query()->updateOrCreate(
            ['key' => 'free_requests'],
            [
                'key' => 'free_requests',
                'value' => '100'
            ]
        );
    }
}
