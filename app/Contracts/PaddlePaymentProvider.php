<?php

namespace App\Contracts;

use App\Models\PricingTier;
use App\Models\SiteConfig;
use App\Models\User;
use App\Services\CustomAuditingService;
use App\Services\ToastMessageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Paddle\Cashier;

class PaddlePaymentProvider implements PaymentProviderContract
{

    public function renderPaymentMethodCollectionPage(Request $request): \Inertia\Response
    {
        if (!$request->user()->is_subscribed) {
            return Inertia::render('PaymentMethodCollectionPage', [
                'price_id' => SiteConfig::getConfig('price_id_for_payment_method_collection')
            ]);
        } else {
            abort(401, 'User already has payment method');
        }
    }

    public function renderPaymentMethodUpdatePage(Request $request, CustomAuditingService $auditService): \Inertia\Response
    {
        if ($request->user()->subscription()) {
            $response = Cashier::api('GET', 'subscriptions/' . $request->user()->subscription()->asPaddleSubscription()['id'] . '/update-payment-method-transaction');

            return Inertia::render('PaymentMethodCollectionPage', [
                'txn_id' => $response->object()?->data?->id
            ]);
        } else {
            $auditService->createCustomAudit($request->user(), 'Attempt to load payment method modification page without subscription');
            abort(401);
        }
    }

    public function saveCard(Request $request, ToastMessageService $toastMessageService, CustomAuditingService $auditService): void
    {
        $user = $request->user();

        try {
            $auditService->createCustomAudit($user, 'Paddle Checkout response', $request->all());

            $card_details = $request->get('data')['payment']['method_details']['card'] ?? null;
            $customer_id = $request->get('data')['customer']['id'] ?? null;
            if ($card_details && ($customer_id == $user->customer->paddle_id)) {
                //Updating customers card details and subscription renewal dates on our end
                $user->update([
                    'card_type' => $card_details['type'],
                    'card_last_4' => $card_details['last4'],
                    'card_expiry_date' => Carbon::create($card_details['expiry_year'], $card_details['expiry_month']),
                    'previous_billing_date' => null,
                    'current_billing_date' => Carbon::now()->addMonth(),
                    'is_active' => true,
                    'user_inactivity_message' => null
                ]);

                $toastMessageService->showToastMessage('success', 'Payment Method Saved Successfully');
            } else {
                //Customer id mismatch or invalid card details
                $auditService->createCustomAudit($user, 'Error in saving payment details', [
                    'card_details' => (bool)$card_details,
                    'customer_id_in_paddle_checkout' => $customer_id,
                    'customer_id_in_auth_user' => $user->customer->paddle_id
                ]);
                $toastMessageService->showToastMessage('error', 'Error in saving payment details');
            }
        } catch (\Exception $e) {
            $toastMessageService->showToastMessage('error', 'Error in saving payment details');

            $auditService->createCustomAudit($user, 'Exception when saving payment details', [
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function removePaymentMethod(User $user): void
    {
        $user->subscription()->cancelNow();
    }

    public function chargeCustomer(User $user, PricingTier $pricing_tier, int $usage): \Illuminate\Http\Client\Response
    {
        return Cashier::api('POST', 'subscriptions/' . $user->subscription()->asPaddleSubscription()['id'] . '/charge', [
            'effective_from' => 'immediately',
            'items' => [
                [
                    'price_id' => $pricing_tier->paddle_pricing_id,
                    'quantity' => $usage
                ]
            ]
        ]);
    }
}
