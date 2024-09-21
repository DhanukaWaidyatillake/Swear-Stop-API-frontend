<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\SiteConfig;
use App\Models\User;
use App\Services\ToastMessageService;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): \Inertia\Response
    {
        return Inertia::render('Profile/Edit', [
            'api_key' => $this->decrypt_token($request->user())
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    public function refresh_token(Request $request, ToastMessageService $toastMessageService)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->decrypt_token($request->user())
        ])->post(Config::get('auth.api_app_url') . '/api/refresh-token', [
            'user_id' => $request->user()->id,
        ]);

        if (!$response->successful()) {
            $toastMessageService->showToastMessage('error', $response->reason());
        } else {
            $toastMessageService->showToastMessage('success', 'API Token Refreshed');
        }
    }

    private function decrypt_token(User $user)
    {
        $key = config('app.key');
        $data = base64_decode($user->apiKeys()->latest()->first()->encrypted_api_key);
        $ivLength = openssl_cipher_iv_length(config('app.encryption_algorithm'));
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
    }
}
