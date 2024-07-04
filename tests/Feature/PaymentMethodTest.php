<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
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

        $user=$this->createUser();

        //Checking if paddle customer is created during registration
        $this->assertNotNull($user->customer);
    }


    public function test_payment_method_addition_page_can_be_rendered()
    {
        $user=$this->createUser();

        $response = $this->actingAs($user)->get('/load-payment-details-page');

        $response->assertSuccessful();
    }

    public function test_payment_method_can_be_added()
    {
        $this->withoutExceptionHandling();

        $user=$this->createUser();

        $files=File::allFiles('tests/Data/Add_payment_method');

        foreach ($files as $file) {
            $json = File::get($file->getPathname());
            $data = json_decode($json, true);

            array_walk_recursive($data, function (&$value, $key) use ($user,&$updated) {
                if ($key === 'customer_id') {
                    $value = $user->customer->id;
                    $updated = true;
                }
            });

            $res = $this->post('/paddle/webhook',$data);

            $res->assertSuccessful();
        }

    }

//    public function test_payment_method_addition_page_cannot_be_rendered_if_user_has_payment_method()
//    {
//        $user=$this->createUser();
//
//        $user->update([
//           'is_subscribed' => true
//        ]);
//
//        $response = $this->actingAs($user)->get('/load-payment-details-page');
//
//        $response->assertServerError();
//    }
}
