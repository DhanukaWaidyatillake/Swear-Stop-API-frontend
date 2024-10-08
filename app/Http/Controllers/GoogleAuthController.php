<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ApiKeyCreationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        Log::info(json_encode($google_account, JSON_PRETTY_PRINT));
        if (!empty($google_account)) {

            $isExistUser = User::query()->where('google_id', $google_account->id)->exists();

            $user = User::updateOrCreate([
                'google_id' => $google_account->id,
            ], [
                'name' => $google_account->name,
                'email' => $google_account->email,
                'password' => Hash::make(Str::random(8)),
                'google_account_info' => json_encode($google_account, JSON_PRETTY_PRINT),
                'is_oauth_user' => true
            ]);

            if (!$isExistUser) {
                //ping the api app to generate the API key and finalize registration if user is registering
                $apiKeyCreationService->createAPIKeyAndFinalizeRegistration($user);
            }

            Auth::login($user);

            return redirect(route('dashboard', absolute: false));
        }
        return false;
    }
}
