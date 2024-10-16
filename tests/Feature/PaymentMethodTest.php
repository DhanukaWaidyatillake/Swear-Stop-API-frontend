<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use WithFaker;

    private function createUser(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        Artisan::call('signup_secret:refresh');

        $response = $this->post('/register', [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));

        return auth()->user();
    }

    public function test_manage_payments_page_can_be_rendered()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/payments');

        $response->assertStatus(200);
    }

    public function test_customer_created_as_paddle_customer_during_registration()
    {

        $user = $this->createUser();

        //Checking if paddle customer is created during registration
        $this->assertNotNull($user->customer);
    }


    public function test_payment_method_addition_page_can_be_rendered()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/save-card');

        $response->assertSuccessful();
    }

    public function test_payment_method_can_be_added()
    {
        $this->withoutExceptionHandling();

        $user = $this->createUser();

        $jsonData = file_get_contents(base_path('tests/Data/paddle_js_checkout_response.json'));

        $jsonData = json_decode($jsonData, true);

        $jsonData['data']['customer']['id'] = $user->customer->paddle_id;

        $response = $this->actingAs($user)->post('/card-saved-successfully', $jsonData);

        $response->assertSuccessful();

        $user->refresh();

        $this->assertEquals($user->card_last_4, $jsonData['data']['payment']['method_details']['card']['last4']);
        $this->assertEquals($user->card_type, $jsonData['data']['payment']['method_details']['card']['type']);
        $response->assertSessionHas('toast', [
            'type' => 'success',
            'message' => 'Payment Method Saved Successfully'
        ]);
    }

    public function test_payment_method_cannot_be_added_if_customer_id_mismatch()
    {
        $this->withoutExceptionHandling();

        $user = $this->createUser();

        $jsonData = file_get_contents(base_path('tests/Data/paddle_js_checkout_response.json'));

        $jsonData = json_decode($jsonData, true);

        $jsonData['data']['customer']['id'] = Str::random(10);

        $response = $this->actingAs($user)->post('/card-saved-successfully', $jsonData);

        $user->refresh();

        $this->assertNull($user->card_last_4);
        $this->assertNull($user->card_type);
        $response->assertSessionHas('toast', [
            'type' => 'error',
            'message' => 'Error in saving payment details'
        ]);
    }

    public function test_payment_method_modification_page_cannot_be_rendered_if_user_has_no_subscription()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get('/update-card');

        $response->assertUnauthorized();
    }
}
