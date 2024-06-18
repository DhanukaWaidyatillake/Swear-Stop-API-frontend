<?php

namespace App\Http\Controllers;

use App\Models\SiteConfig;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): \Symfony\Component\HttpFoundation\RedirectResponse|\Illuminate\Http\RedirectResponse
    {
        $a = 10;
        $a = str($a);
        return Socialite::driver('google')->redirect();
    }

    public function callback(): false|\Illuminate\Http\RedirectResponse
    {
        $google_account = Socialite::driver('google')->user();
        if (!empty($google_account)) {
            $user = User::updateOrCreate([
                'google_id' => $google_account->id,
            ], [
                'name' => $google_account->name,
                'email' => $google_account->email,
                'password' => Hash::make(Str::random(8)),
                'google_account_info' => json_encode($google_account, JSON_PRETTY_PRINT)
            ]);

            //ping the api app to generate the sanctum token
            $response = Http::post(Config::get('auth.api_app_url') . '/generate-token', [
                'user_id' => $user->id,
                'signup_secret' => SiteConfig::firstWhere('key', 'signup_secret')?->value
            ]);

            if ($response->ok()) {

                //refresh the signup token
                Artisan::call('signup_secret:refresh');

                $user->update([
                    'is_signup_successful' => true
                ]);

                Auth::login($user, true);

                return redirect()->intended(route('dashboard', absolute: false));
            } else {
                throw new \Exception('Error in generating sanctum token', 500);
            }
        }
        return false;
    }
}
