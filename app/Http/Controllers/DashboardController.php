<?php

namespace App\Http\Controllers;

use App\Enums\ChartFilters;
use App\Http\Requests\ChartFilterRequest;
use App\Models\TextFilterAudit;
use App\Services\ApiResultTools;
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
        return response()->json([array_keys($profanityFrequency), array_values($profanityFrequency)], 200);
    }

    public function loadProfanityCategoryChart(ChartFilterRequest $request): \Illuminate\Http\JsonResponse
    {
        $profanityCategory = ChartFilters::tryFrom($request->get('value'))?->profanityCategoryPercentage($request->user());

        $total = array_sum(array_values($profanityCategory));
        $values = array_map(function ($item) use ($total) {
            return round((($item * 100) / $total), 2);
        }, array_values($profanityCategory));

        return response()->json([array_keys($profanityCategory), $values], 200);
    }

    public function loadProfanityFilterHistory(Request $request, ApiResultTools $apiResultTools): \Illuminate\Contracts\Pagination\LengthAwarePaginator|array
    {
        $query = TextFilterAudit::query()->when($request->profanity_detected_only, function ($query) {
            return $query->whereNotNull('profanity_caught');
        })->where('user_id', $request->user()->id);

        return $apiResultTools->setRequestAndQuery($query, $request)->search()->order()->paginate(5);
    }

    public function loadRequestDetailsPopup(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json(TextFilterAudit::query()->findOrFail($request->request_id));
    }

    public function loadChartFilters(): \Illuminate\Http\JsonResponse
    {
        return response()->json(ChartFilters::cases(), 200);
    }
}
