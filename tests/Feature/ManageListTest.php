<?php

namespace Tests\Feature;

use App\Models\BlacklistedWord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ManageListTest extends TestCase
{
    use WithFaker,RefreshDatabase;

    public function test_manage_list_page_can_be_rendered()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/manage_list');

        $response->assertStatus(200);
    }

    public function test_adding_valid_blacklist_word()
    {
        $user = User::factory()->create();

        $word = $this->faker->word;

        $response = $this->actingAs($user)->post('/add_word_to_blacklist', [
            'word' => $word
        ]);

        $response->assertStatus(302);

        $blacklisted_word = BlacklistedWord::query()->where('word', $word)->first();

        $this->assertNotNull($blacklisted_word);

        $this->assertTrue((bool)$blacklisted_word->is_enabled);

        $this->assertEquals('dashboard', $blacklisted_word->added_through);
    }

    public function test_duplicate_blacklist_words_cannot_be_added_for_same_user()
    {

        $user = User::factory()->create();

        $word = $this->faker->word;


        $this->actingAs($user)->post('/add_word_to_blacklist', [
            'word' => $word
        ]);

        $this->expectException(ValidationException::class);

        $response = $this->withoutExceptionHandling()->actingAs($user)->post('/add_word_to_blacklist', [
            'word' => $word
        ]);

        $response->assertStatus(302);

        $response->assertJsonValidationErrorFor('word');

        $blacklisted_word = BlacklistedWord::query()->where('word', $word)->first();

        $this->assertNull($blacklisted_word);
    }

    public function test_cannot_add_empty_blacklist_word()
    {

        $user = User::factory()->create();


        $response = $this->actingAs($user)->post('/add_word_to_blacklist', [
            'word' => ''
        ]);

        $this->expectException(ValidationException::class);

        $response->assertStatus(302);

        $response->assertJsonValidationErrorFor('word');
    }
}
