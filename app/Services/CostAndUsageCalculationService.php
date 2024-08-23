<?php

namespace App\Services;

use App\Models\PricingTier;
use App\Models\TextFilterAudit;
use App\Models\User;

class CostAndUsageCalculationService
{
    public function calculateCostAndUsageForCurrentBillingMonth(User $user): array
    {
        $start_of_billing_month = $user->previous_billing_date ?? $user->current_billing_date->subMonth();

        $usage = TextFilterAudit::query()->where('user_id', $user->id)
            ->where('is_successful', true)
            ->where('is_free_request', false)
            ->where('created_at', '>', $start_of_billing_month)
            ->where('created_at', '<=', $user->current_billing_date)
            ->count();

        $highest_tier = PricingTier::query()->orderBy('from', 'desc')->first();

        if ($usage >= $highest_tier->from) {
            return [
                'usage' => $usage,
                'cost' => round($highest_tier->price_per_api_call * $usage, 1)
            ];
        } else {
            if ($usage == 0) {
                return [
                    'usage' => 0,
                    'cost' => 0.0
                ];
            } else {
                $tier = PricingTier::query()
                    ->select('price_per_api_call')
                    ->where('from', '>=', $usage)
                    ->where('to', '<=', $usage)
                    ->first();

                return [
                    'usage' => $usage,
                    'cost' => round($tier->price_per_api_call * $usage, 1)
                ];
            }
        }
    }

    public function calculateCostAndUsageForGivenBillingMonth(User $user)
    {

    }
}
