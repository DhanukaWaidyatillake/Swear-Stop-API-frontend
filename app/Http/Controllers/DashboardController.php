<?php

namespace App\Http\Controllers;

use App\Enums\ChartFilters;
use App\Http\Requests\ChartFilterRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request): \Inertia\Response
    {
        return Inertia::render('Dashboard');
    }

    public function loadProfanityFrequencyChart(ChartFilterRequest $request): \Illuminate\Http\JsonResponse
    {
        $profanityFrequency = ChartFilters::tryFrom($request->get('value'))?->profanityFrequency($request->user());

        return response()->json([array_keys($profanityFrequency),array_values($profanityFrequency)], 200);
    }

    public function loadProfanityCategoryChart()
    {

    }

    public function loadChartFilters(): \Illuminate\Http\JsonResponse
    {
        return response()->json(ChartFilters::cases(), 200);
    }
}
