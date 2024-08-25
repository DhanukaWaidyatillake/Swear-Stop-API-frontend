<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\User;
use App\Services\CustomAuditingService;
use Illuminate\Console\Command;
use Laravel\Paddle\Cashier;

class TriggerSubscriptionRenewal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trigger-subscription-renewal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Triggering subscription renewal';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\CostAndUsageCalculationService $costAndUsageCalculationService, CustomAuditingService $auditingService)
    {
        //Get all Users whose billing date has elapsed and have an active subscription
        $users = \App\Models\User::query()
            ->where('current_billing_date', '<=', \Carbon\Carbon::now())
            ->get()->filter(function (User $user) {
                return $user->is_subscribed;
            });

        foreach ($users as $user) {
            $current_date = \Carbon\Carbon::now();

            $invoice = new Invoice();
            $invoice->user_id = $user->id;
            $invoice->billing_from_date = $user->previous_billing_date ?? $user->current_billing_date->subMonth();
            $invoice->billing_to_date = $user->current_billing_date;

            $data = $costAndUsageCalculationService->calculateCostAndUsageForCurrentBillingMonth($user);
            $invoice->total = $data['cost'];
            $invoice->api_usage = $data['usage'];
            $invoice->save();

            if ($data['usage'] != 0) {
                //User has API usage for the month

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
                    $user->update([
                        'current_billing_date' => $current_date->clone()->addMonth(),
                        'previous_billing_date' => $current_date,
                        'is_active' => true,
                        'current_month_failed_renewal_attempts' => 0
                    ]);
                    $auditingService->createCustomAudit($user,
                        'Subscription renewed successfully', [
                            ...$response->json(),
                            'invoice_id' => $invoice->id
                        ]
                    );
                } else {
                    //Paddle response failed

                    $invoice->is_paid = false;
                    $auditingService->createCustomAudit($user,
                        'Subscription renewal failed', [
                            ...$response->json(),
                            'invoice_id' => $invoice->id
                        ]
                    );

                    //Incrementing failed renewal attempts
                    $user->increment('current_month_failed_renewal_attempts');
                }

            } else {
                //User doesn't have API usage for the month

                $invoice->paddle_response = null;
                $user->update([
                    'current_billing_date' => $current_date->clone()->addMonth(),
                    'previous_billing_date' => $current_date
                ]);

                $invoice->is_paid = true;

                $auditingService->createCustomAudit($user,
                    'Subscription renewed successfully', [
                        'no_usage' => true,
                        'invoice_id' => $invoice->id
                    ]
                );
            }

            //Updating the invoice with the changes
            $invoice->update();
        }
    }
}
