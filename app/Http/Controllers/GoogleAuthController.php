<?php

namespace App\Http\Controllers;

use App\Models\SiteConfig;
use App\Models\User;
use App\Services\ApiKeyCreationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): \Symfony\Component\HttpFoundation\RedirectResponse|\Illuminate\Http\RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * @throws \Exception
     */
    public function callback(ApiKeyCreationService $apiKeyCreationService): false|\Illuminate\Http\RedirectResponse
    {
        $google_account = Socialite::driver('google')->user();
        Log::info(json_encode($google_account,JSON_PRETTY_PRINT));
        if (!empty($google_account)) {
            $user = User::updateOrCreate([
                'google_id' => $google_account->id,
            ], [
                'name' => $google_account->name,
                'email' => $google_account->email,
                'password' => Hash::make(Str::random(8)),
                'google_account_info' => json_encode($google_account, JSON_PRETTY_PRINT)
            ]);

            //ping the api app to generate the API key and finalize registration
            $apiKeyCreationService->createAPIKeyAndFinalizeRegistration($user);

            Auth::login($user);

            return redirect(route('dashboard', absolute: false));
        }
        return false;
    }
}
