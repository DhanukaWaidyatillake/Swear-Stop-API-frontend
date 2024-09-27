<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiTestFormRequest;
use App\Models\PricingTier;
use App\Models\ProfanityCategory;
use App\Models\TestSentence;
use App\Services\PaymentProcessingService;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
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
            'api_domain' => Config::get('auth.api_external_url'),
            'maxUsage' => PricingTier::query()->orderBy('from', 'desc')->firstOrFail()->from
        ]);
    }

    public function getRandomSentence(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['sentence' => TestSentence::query()->select('id', 'sentence')->inRandomOrder()->firstOrFail()]);
    }

    public function getMonthlyCost(Request $request, PaymentProcessingService $paymentProcessingService)
    {
        $slider_value = $request->slider_value;
        $highest_tier = PricingTier::query()->orderBy('from', 'desc')->firstOrFail();
        $usage = round(($slider_value / 100) * $highest_tier->from);
        return [
            'usage' => $usage,
            'cost' => $paymentProcessingService->calculateCost($usage)['cost']
        ];
    }
}
