<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): \Symfony\Component\HttpFoundation\RedirectResponse|\Illuminate\Http\RedirectResponse
    {
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
//            dd($user->password);
//            auth()->login($user);
            Auth::login($user,true);
//            Auth::attempt([
//                'email' => $google_account->email
//            ], true);

            return redirect()->intended(route('dashboard', absolute: false));
        }
        return false;
    }
}
