<?php

namespace App\Services;

use App\Models\SiteConfig;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class ApiKeyCreationService
{
    public function createAPIKeyAndFinalizeRegistration(User $user): void
    {
        $response = Http::post(Config::get('auth.api_app_url') . '/api/generate-token', [
            'user_id' => $user->id,
            'signup_secret' => SiteConfig::query()->firstWhere('key', 'signup_secret')?->value
        ]);


        if ($response->ok()) {
            //refresh the signup token
            Artisan::call('signup_secret:refresh');

            $user->update([
                'is_signup_successful' => true
            ]);

            //Create paddle customer
            $user->createAsCustomer();

            $user->update([
                'free_request_count' => (int) SiteConfig::query()->firstWhere('key', 'free_requests')?->value
            ]);

        } else {
            throw new \Exception('Error in generating signup_secret token', 500);
        }
    }
}
