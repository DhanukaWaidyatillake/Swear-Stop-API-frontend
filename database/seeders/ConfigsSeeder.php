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

        SiteConfig::query()->updateOrCreate(
            ['key' => 'user_inactivity_message_for_no_card'],
            [
                'key' => 'user_inactivity_message_for_no_card',
                'value' => "It looks like you've run out of free requests and don't have a payment method on file. To continue using the Swear-Stop API, please add your card details at http://localhost:8086/payments"
            ]
        );

        SiteConfig::query()->updateOrCreate(
            ['key' => 'price_id_for_payment_method_collection'],
            [
                'key' => 'price_id_for_payment_method_collection',
                'value' => 'pri_01j17pahhf3d620xya4x9ckrg7'
            ]
        );

        SiteConfig::query()->updateOrCreate(
            ['key' => 'payment_processing_fee'],
            [
                'key' => 'payment_processing_fee',
                'value' => '0.5'
            ]
        );
    }
}
