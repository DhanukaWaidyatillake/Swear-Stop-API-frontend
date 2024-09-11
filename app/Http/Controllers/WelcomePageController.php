<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiTestFormRequest;
use App\Models\ProfanityCategory;
use App\Models\TestSentence;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class WelcomePageController extends Controller
{
    public function loadWelcomePage(): \Inertia\Response
    {
        $profanity_categories = ProfanityCategory::query()->select('id', 'profanity_category_name')->get();

        //Adding the 'All Categories' category
        $all_profanity_category = new ProfanityCategory();
        $all_profanity_category->id = 0;
        $all_profanity_category->profanity_category_name = "All Categories";
        $profanity_categories->add($all_profanity_category);

        return Inertia::render('Welcome', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'profanityCategories' => $profanity_categories,
            'laravelVersion' => Application::VERSION,
            'phpVersion' => PHP_VERSION, 99,
            'api_domain' => App::isLocal() ? 'http://localhost:8087' : Config::get('auth.api_app_url')
        ]);
    }

    public function getRandomSentence(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['sentence' => TestSentence::query()->select('id', 'sentence')->inRandomOrder()->firstOrFail()]);
    }
}
