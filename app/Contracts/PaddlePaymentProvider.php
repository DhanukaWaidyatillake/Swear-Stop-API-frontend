<?php

namespace App\Contracts;

use App\Models\Invoice;
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
    private CustomAuditingService $customAuditingService;

    private ToastMessageService $toastMessageService;

    public function __construct(CustomAuditingService $customAuditingService, ToastMessageService $toastMessageService)
    {
        $this->customAuditingService = $customAuditingService;
        $this->toastMessageService = $toastMessageService;
    }

    public function addCustomer(User $user): void
    {
        //Create customer in paddle
        $user->createAsCustomer();
    }

    public function renderPaymentMethodCollectionPage(Request $request): \Inertia\Response
    {
        if (!$request->user()->is_subscribed) {
            return Inertia::render('PaddlePaymentMethodCollectionPage', [
                'price_id' => SiteConfig::getConfig('price_id_for_payment_method_collection')
            ]);
        } else {
            abort(401, 'User already has payment method');
        }
    }

    public function renderPaymentMethodUpdatePage(Request $request): \Inertia\Response
    {
        if ($request->user()->subscription()) {
            $response = Cashier::api('GET', 'subscriptions/' . $request->user()->subscription()->asPaddleSubscription()['id'] . '/update-payment-method-transaction');

            return Inertia::render('PaddlePaymentMethodCollectionPage', [
                'txn_id' => $response->object()?->data?->id
            ]);
        } else {
            $this->customAuditingService->createCustomAudit($request->user(), 'Attempt to load payment method modification page without subscription');
            abort(401);
        }
    }

    public function saveCard(Request $request): null
    {
        $user = $request->user();

        try {
            $this->customAuditingService->createCustomAudit($user, 'Paddle Checkout response', $request->all());

            $card_details = $request->get('data')['payment']['method_details']['card'] ?? null;
            $customer_id = $request->get('data')['customer']['id'] ?? null;
            if ($card_details && ($customer_id == $user->customer->paddle_id)) {
                //Updating customers card details and subscription renewal dates on our end
                $user_updates = [
                    'card_type' => $card_details['type'],
                    'card_last_4' => $card_details['last4'],
                    'card_expiry_date' => Carbon::create($card_details['expiry_year'], $card_details['expiry_month']),
                ];

                //User adding first payment method
                if (is_null($user->current_billing_date)) {
                    $user_updates[] = [
                        'previous_billing_date' => null,
                        'current_billing_date' => Carbon::now()->addMonth(),
                        'user_inactivity_message' => null,
                        'is_active' => true,
                    ];
                }

                $user->update($user_updates);

                $this->toastMessageService->showToastMessage('success', 'Payment Method Saved Successfully');
            } else {
                //Customer id mismatch or invalid card details
                $this->customAuditingService->createCustomAudit($user, 'Error in saving payment details', [
                    'card_details' => (bool)$card_details,
                    'customer_id_in_paddle_checkout' => $customer_id,
                    'customer_id_in_auth_user' => $user->customer->paddle_id
                ]);
                $this->toastMessageService->showToastMessage('error', 'Error in saving payment details');
            }
        } catch (\Exception $e) {
            $this->toastMessageService->showToastMessage('error', 'Error in saving payment details');

            $this->customAuditingService->createCustomAudit($user, 'Exception when saving payment details', [
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
        }

        return null;
    }

    public function removePaymentMethod(User $user): void
    {
        $user->subscription()->cancelNow();
    }

    public function chargeCustomer(User $user, Invoice $invoice, PricingTier $pricing_tier, int $usage): bool
    {
        //TODO : Investigate how to charge the minimum amount

        $response = Cashier::api('POST', 'subscriptions/' . $user->subscription()->asPaddleSubscription()['id'] . '/charge', [
            'effective_from' => 'immediately',
            'items' => [
                [
                    'price_id' => $pricing_tier->paddle_pricing_id,
                    'quantity' => $usage
                ]
            ]
        ]);

        if ($response->successful()) {
            //Successfully Charged the customers card
            $invoice->is_paid = true;
            $invoice->update();

            $this->customAuditingService->createCustomAudit($user,
                'Paddle Payment made successfully', [
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
                'Paddle Payment failed', [
                    ...$response->json(),
                    'invoice_id' => $invoice->id
                ]
            );
            return false;
        }
    }
}
