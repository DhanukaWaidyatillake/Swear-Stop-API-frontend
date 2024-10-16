<?php

namespace App\Services;

use App\Contracts\PaymentProviderManager;
use App\Models\Invoice;
use App\Models\PricingTier;
use App\Models\TextFilterAudit;
use App\Models\User;

class PaymentProcessingService
{
    private CustomAuditingService $customAuditingService;
    private PaymentProviderManager $paymentProviderManager;

    public function __construct(CustomAuditingService $customAuditingService, PaymentProviderManager $paymentProviderManager)
    {
        $this->customAuditingService = $customAuditingService;
        $this->paymentProviderManager = $paymentProviderManager;
    }

    public function calculateCost(int $usage = 0): array
    {
        $highest_tier = PricingTier::query()->orderBy('from', 'desc')->firstOrFail();

        if ($usage >= $highest_tier->from) {
            $tier = $highest_tier;
            //If usage is in the highest tier
        } else {
            $tier = PricingTier::query()
                ->select('price_per_api_call', 'paddle_pricing_id')
                ->where('to', '>=', $usage)
                ->orderBy('from')
                ->firstOrFail();
        }

        return [
            'cost' => $tier->price_per_api_call * $usage,
            'tier' => $tier
        ];
    }

    public function calculateCostAndUsageForCurrentBillingMonth(User $user): array
    {
        if (!is_null($user->current_billing_date)) {
            $start_of_billing_month = $user->previous_billing_date ?? $user->current_billing_date->subMonth();

            $usage = TextFilterAudit::query()->where('user_id', $user->id)
                ->where('is_successful', true)
                ->where('is_free_request', false)
                ->where('created_at', '>', $start_of_billing_month)
                ->where('created_at', '<=', $user->current_billing_date)
                ->count();
        } else {
            $usage = 0;
        }

        $costAndTier = $this->calculateCost($usage);

        return [
            'usage' => $usage,
            'cost' => $costAndTier['cost'],
            'pricing_tier' => $costAndTier['tier']
        ];
    }

    public function chargeCustomer(User $user): bool
    {
        $data = $this->calculateCostAndUsageForCurrentBillingMonth($user);

        $invoice = new Invoice();
        $invoice->user_id = $user->id;
        $invoice->billing_from_date = $user->previous_billing_date ?? $user->current_billing_date->subMonth();
        $invoice->billing_to_date = $user->current_billing_date;

        $invoice->total = $data['cost'];
        $invoice->api_usage = $data['usage'];
        $invoice->save();

        try {
            if ($data['cost'] > 0) {
                //Charging customer's card through Paddle
                return $this->paymentProviderManager->usingDefault()->chargeCustomer($user, $invoice, $data['pricing_tier'], $data['usage']);

            } else {
                //Creating a $0 invoice

                $invoice->is_paid = true;
                $invoice->update();

                $this->customAuditingService->createCustomAudit($user,
                    '$0 Payment made', [
                        'invoice_id' => $invoice->id
                    ]
                );

                return true;
            }
        } catch (\Exception $e) {
            $this->customAuditingService->createCustomAudit($user,
                'Exception when charging customer', [
                    'message' => $e->getMessage(),
                    'details' => $e->getTraceAsString()
                ]
            );

            return false;
        }
    }
}
