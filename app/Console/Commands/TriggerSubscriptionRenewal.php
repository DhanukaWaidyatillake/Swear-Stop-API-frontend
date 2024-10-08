<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\CustomAuditingService;
use App\Services\PaymentProcessingService;
use Illuminate\Console\Command;

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
    public function handle(CustomAuditingService $auditingService, PaymentProcessingService $paymentProcessingService)
    {
        //Get all Users whose billing date has elapsed and have an active subscription
        $users = \App\Models\User::query()
            ->whereNotNull('current_billing_date')
            ->where('current_billing_date', '<=', \Carbon\Carbon::now())
            ->get()->filter(function (User $user) {
                return $user->is_subscribed;
            });

        foreach ($users as $user) {
            $current_date = \Carbon\Carbon::now();

            //User has API usage for the month
            $isSuccessful = $paymentProcessingService->chargeCustomer($user, $auditingService);

            if ($isSuccessful) {
                //Successfully charged the customers card
                $user->update([
                    'current_billing_date' => $current_date->clone()->addMonth(),
                    'previous_billing_date' => $current_date,
                    'is_active' => true,
                    'current_month_failed_renewal_attempts' => 0
                ]);

                $auditingService->createCustomAudit($user, 'Subscription renewed successfully');
            } else {
                //Failed to charge customers card
                $auditingService->createCustomAudit($user, 'Subscription renewal failed');

                //Incrementing failed renewal attempts
                $user->increment('current_month_failed_renewal_attempts');
            }
        }
    }
}
