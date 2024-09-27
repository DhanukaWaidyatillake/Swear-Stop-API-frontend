<?php

namespace Tests\Feature\Auth;

use App\Models\SiteConfig;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use WithFaker;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $this->withoutExceptionHandling();

        Artisan::call('signup_secret:refresh');

        $response = $this->post('/register', [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_valid_api_token_generated_during_registration()
    {
        $this->withoutExceptionHandling();

        Artisan::call('signup_secret:refresh');


         $this->post('/register', [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $encrypted_api_key = auth()->user()->apiKeys()->first()?->encrypted_api_key;

        $this->assertNotNull($encrypted_api_key);

        $key = config('app.key');
        $data = base64_decode($encrypted_api_key);
        $ivLength = openssl_cipher_iv_length(config('app.encryption_algorithm'));
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        $decrypted_api_key = openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);

        $this->assertIsString($decrypted_api_key);
    }

    public function test_api_token_cannot_be_generated_with_incorrect_signup_token(): void
    {
        $this->withoutExceptionHandling();

        Artisan::call('signup_secret:refresh');

        $user = User::factory()->createOne();

        $response = Http::post(Config::get('auth.api_internal_url') . '/api/generate-token', [
            'user_id' => $user->id,
            'signup_secret' => Str::uuid()->toString()
        ]);

        $this->assertEquals(403, $response->status());
    }

    public function test_signup_secret_refreshed_after_successful_registration(): void
    {
        $this->withoutExceptionHandling();

        Artisan::call('signup_secret:refresh');

        $initial_signup_secret = SiteConfig::firstWhere('key', 'signup_secret')?->value;

        $this->post('/register', [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $final_signup_secret = SiteConfig::firstWhere('key', 'signup_secret')?->value;


        $this->assertNotEquals($initial_signup_secret, $final_signup_secret);
    }
}
