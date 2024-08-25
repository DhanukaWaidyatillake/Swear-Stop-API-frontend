<?php

namespace App\Http\Middleware;

use App\Models\SiteConfig;
use App\Models\WebhookLog;
use Closure;
use Illuminate\Http\Request;
use Laravel\Paddle\Cashier;
use Symfony\Component\HttpFoundation\Response;

class AuditPaddleWebhooks
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('paddle/webhook')) {
            //Creating A webhook Log
            WebhookLog::query()->create([
                'user_id' => isset($request->get('data')['customer_id']) ? Cashier::findBillable($request->get('data')['customer_id'])->id : null,
                'webhook_data' => json_encode($request->toArray(), JSON_PRETTY_PRINT)
            ]);

            if ($request->get('event_type') == "subscription.activated") {

                //Change the billing date of the subscription to a date in the late future to prevent Paddle auto-renewal
                Cashier::api('PATCH', 'subscriptions/' . $request->get('data')['id'], [
                    'next_billed_at' => SiteConfig::query()->firstWhere('key', 'subscription_renewal_date')?->value,
                    'proration_billing_mode' => 'do_not_bill'
                ]);
            }
        }
        return $next($request);
    }
}
