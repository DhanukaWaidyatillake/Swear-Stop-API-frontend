<?php

namespace Tests\Feature;

use App\Models\BlacklistedWord;
use App\Models\User;
use App\Models\WhitelistedWord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Random\RandomException;
use Tests\TestCase;

class ManageListTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    public function test_manage_list_page_can_be_rendered()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/manage_list');

        $response->assertStatus(200);
    }


    //Blacklist tests ...
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

    public function test_blacklist_get_all()
    {
        $user = User::factory()->create();

        $words = $this->faker->words(100);

        foreach ($words as $word) {
            BlacklistedWord::create([
                'word' => $word,
                'is_enabled' => true,
                'added_through' => 'dashboard',
                'user_id' => $user->id
            ]);
        }

        $response = $this->actingAs($user)->get('/get_blacklisted_words?page=1&search=');

        $response->assertStatus(200);

        //assert that the result is paginated
        $response->assertSee('next_page_url');
        $response->assertSee('prev_page_url');
        $response->assertSee('links');
        $response->assertSee('last_page');
    }

    /**
     * @throws RandomException
     */
    public function test_blacklist_search_and_pagination()
    {
        $user = User::factory()->create();
        $words = $this->faker->words(100);
        $searchString = Str::lower(Str::random(2));

        foreach ($words as $word) {
            BlacklistedWord::create([
                'word' => $this->faker->boolean() ? $word : $word . $searchString,
                'is_enabled' => true,
                'added_through' => 'dashboard',
                'user_id' => $user->id
            ]);
        }

        $actual_word_count = BlacklistedWord::query()->where('word', 'like', '%' . $searchString . '%')->count();
        $page = random_int(1, floor($actual_word_count / 5));

        $response = $this->actingAs($user)->call('GET', '/get_blacklisted_words', [
            'page' => $page,
            'search' => [
                'search_field' => 'word',
                'search_string' => $searchString
            ]
        ]);

        $response->assertSuccessful();

        $this->assertEquals($response->json('total'), $actual_word_count);
        $this->assertEquals($response->json('current_page'), $page);
    }

    public function test_enable_disable_blacklisted_word()
    {
        $user = User::factory()->create();

        $blacklistedWord=BlacklistedWord::query()->create([
            'word' => $this->faker->word,
            'is_enabled' => true,
            'added_through' => 'dashboard',
            'user_id' => $user->id
        ]);

        $this->actingAs($user)->put('/change_state_blacklist/'.$blacklistedWord->id,[
            'is_enabled' => false
        ]);

        $blacklistedWord->refresh();
        $this->assertFalse((bool)$blacklistedWord->is_enabled);

        $this->actingAs($user)->put('/change_state_blacklist/'.$blacklistedWord->id,[
            'is_enabled' => true
        ]);

        $blacklistedWord->refresh();
        $this->assertTrue((bool)$blacklistedWord->is_enabled);
    }


    //Whitelist tests ...
    public function test_adding_valid_whitelist_word()
    {
        $user = User::factory()->create();

        $word = $this->faker->word;

        $response = $this->actingAs($user)->post('/add_word_to_whitelist', [
            'word' => $word
        ]);

        $response->assertStatus(302);

        $whitelisted_word = WhitelistedWord::query()->where('word', $word)->first();

        $this->assertNotNull($whitelisted_word);

        $this->assertTrue((bool)$whitelisted_word->is_enabled);

        $this->assertEquals('dashboard', $whitelisted_word->added_through);
    }

    public function test_duplicate_whitelist_words_cannot_be_added_for_same_user()
    {

        $user = User::factory()->create();

        $word = $this->faker->word;


        $this->actingAs($user)->post('/add_word_to_whitelist', [
            'word' => $word
        ]);

        $this->expectException(ValidationException::class);

        $response = $this->withoutExceptionHandling()->actingAs($user)->post('/add_word_to_whitelist', [
            'word' => $word
        ]);

        $response->assertStatus(302);

        $response->assertJsonValidationErrorFor('word');

        $whitelisted_word = WhitelistedWord::query()->where('word', $word)->first();

        $this->assertNull($whitelisted_word);
    }

    public function test_cannot_add_empty_whitelist_word()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/add_word_to_whitelist', [
            'word' => ''
        ]);

        $this->expectException(ValidationException::class);

        $response->assertStatus(302);

        $response->assertJsonValidationErrorFor('word');
    }

    public function test_whitelist_get_all()
    {
        $user = User::factory()->create();

        $words = $this->faker->words(100);

        foreach ($words as $word) {
            WhitelistedWord::create([
                'word' => $word,
                'is_enabled' => true,
                'added_through' => 'dashboard',
                'user_id' => $user->id
            ]);
        }

        $response = $this->actingAs($user)->get('/get_whitelisted_words?page=1&search=');

        $response->assertStatus(200);

        //assert that the result is paginated
        $response->assertSee('next_page_url');
        $response->assertSee('prev_page_url');
        $response->assertSee('links');
        $response->assertSee('last_page');
    }

    /**
     * @throws RandomException
     */
    public function test_whitelist_search_and_pagination()
    {
        $user = User::factory()->create();
        $words = $this->faker->words(100);
        $searchString = Str::lower(Str::random(2));

        foreach ($words as $word) {
            WhitelistedWord::create([
                'word' => $this->faker->boolean() ? $word : $word . $searchString,
                'is_enabled' => true,
                'added_through' => 'dashboard',
                'user_id' => $user->id
            ]);
        }

        $actual_word_count = WhitelistedWord::query()->where('word', 'like', '%' . $searchString . '%')->count();
        $page = random_int(1, floor($actual_word_count / 5));

        $response = $this->actingAs($user)->call('GET', '/get_whitelisted_words', [
            'page' => $page,
            'search' => [
                'search_field' => 'word',
                'search_string' => $searchString
            ]
        ]);

        $response->assertSuccessful();

        $this->assertEquals($response->json('total'), $actual_word_count);
        $this->assertEquals($response->json('current_page'), $page);
    }

    public function test_enable_disable_whitelisted_word()
    {
        $user = User::factory()->create();

        $whitelistedWord=WhitelistedWord::query()->create([
            'word' => $this->faker->word,
            'is_enabled' => true,
            'added_through' => 'dashboard',
            'user_id' => $user->id
        ]);

        $this->actingAs($user)->put('/change_state_whitelist/'.$whitelistedWord->id,[
            'is_enabled' => false
        ]);

        $whitelistedWord->refresh();
        $this->assertFalse((bool)$whitelistedWord->is_enabled);

        $this->actingAs($user)->put('/change_state_whitelist/'.$whitelistedWord->id,[
            'is_enabled' => true
        ]);

        $whitelistedWord->refresh();
        $this->assertTrue((bool)$whitelistedWord->is_enabled);
    }
}
