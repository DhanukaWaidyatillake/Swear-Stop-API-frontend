<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SiteConfig;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        event(new Registered($user));

        //ping the api app to generate the sanctum token
        $response = Http::post(Config::get('auth.api_app_url') . '/api/generate-token', [
            'user_id' => $user->id,
            'signup_secret' => SiteConfig::firstWhere('key', 'signup_secret')?->value
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
                'previous_billing_date' => null,
                'current_billing_date' => Carbon::now()->addMonth(),
            ]);

            Auth::login($user);

            return redirect(route('dashboard', absolute: false));
        } else {
            throw new \Exception('Error in generating sanctum token', 500);
        }
    }
}
