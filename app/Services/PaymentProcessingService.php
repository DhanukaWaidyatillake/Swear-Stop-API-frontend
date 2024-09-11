<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PricingTier;
use App\Models\TextFilterAudit;
use App\Models\User;
use Laravel\Paddle\Cashier;

class PaymentProcessingService
{
    private CustomAuditingService $customAuditingService;

    public function __construct(CustomAuditingService $customAuditingService)
    {
        $this->customAuditingService = $customAuditingService;
    }

    public function calculateCost(PricingTier $tier, int $usage = 0): float|int
    {
        return $tier->price_per_api_call * $usage;
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

        $highest_tier = PricingTier::query()->orderBy('from', 'desc')->firstOrFail();

        if ($usage >= $highest_tier->from) {
            return [
                'usage' => $usage,
                'cost' => round($this->calculateCost($highest_tier, $usage), 1),
                'pricing_tier' => null
            ];
        } else {
            if ($usage == 0) {
                return [
                    'usage' => 0,
                    'cost' => 0.0,
                    'pricing_tier' => null
                ];
            } else {
                $tier = PricingTier::query()
                    ->select('price_per_api_call', 'paddle_pricing_id')
                    ->where('to', '>=', $usage)
                    ->orderBy('from')
                    ->firstOrFail();

                return [
                    'usage' => $usage,
                    'cost' => round($this->calculateCost($tier, $usage), 1),
                    'pricing_tier' => $tier
                ];
            }
        }
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
            if ($data['usage'] > 0) {
                //Charging customer's card through Paddle
                $response = Cashier::api('POST', 'subscriptions/' . $user->subscription()->asPaddleSubscription()['id'] . '/charge', [
                    'effective_from' => 'immediately',
                    'items' => [
                        [
                            'price_id' => $data['pricing_tier']->paddle_pricing_id,
                            'quantity' => $data['usage']
                        ]
                    ]
                ]);

                $invoice->paddle_response = $response->json();

                if ($response->successful()) {
                    //Successfully Charged the customers card
                    $invoice->is_paid = true;
                    $invoice->update();

                    $this->customAuditingService->createCustomAudit($user,
                        'Payment made successfully', [
                            ...$response->json(),
                            'invoice_id' => $invoice->id
                        ]
                    );

                    return true;
                } else {
                    //Paddle response failed
                    $invoice->is_paid = false;
                    $invoice->update();

                    $this->customAuditingService->createCustomAudit($user,
                        'Payment failed', [
                            ...$response->json(),
                            'invoice_id' => $invoice->id
                        ]
                    );

                    return false;
                }
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
