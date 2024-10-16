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
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripePaymentProvider implements PaymentProviderContract
{
    private StripeClient $stripeClient;
    private CustomAuditingService $customAuditingService;

    private ToastMessageService $toastMessageService;

    public function __construct(CustomAuditingService $customAuditingService, ToastMessageService $toastMessageService)
    {
        $this->stripeClient = new StripeClient(config('app.stripe_secret'));
        $this->customAuditingService = $customAuditingService;
        $this->toastMessageService = $toastMessageService;
    }

    /**
     * @throws ApiErrorException
     */
    public function addCustomer(User $user): void
    {
        $response = $this->stripeClient->customers->create([
            'name' => $user->name,
            'email' => $user->email,
        ]);

        //Add stripe_customer_id to users table
        $user->update([
            'stripe_customer_id' => $response->id
        ]);

        $this->customAuditingService->createCustomAudit($user, 'Stripe Customer Created', $response->toArray());
    }

    public function renderPaymentMethodCollectionPage(Request $request): \Inertia\Response
    {
        return Inertia::render('StripePaymentMethodCollectionPage', [
            'stripe_publishable_key' => config('app.stripe_key'),
            'client_secret' => $this->stripeClient->setupIntents->create(['payment_method_types' => ['card']])->client_secret
        ]);
    }

    public function renderPaymentMethodUpdatePage(Request $request): \Inertia\Response
    {

        return $this->renderPaymentMethodCollectionPage($request);
    }

    /**
     * @throws ApiErrorException
     */
    public function saveCard(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        try {
            $this->customAuditingService->createCustomAudit($user, 'Stripe Intent Response', $request->all());
            //Obtaining payment method id
            $payment_method_id = $request->setupIntent['payment_method'];
            $payment_method = $this->stripeClient->paymentMethods->retrieve($payment_method_id);

            //Attach payment method to customer
            $payment_method->attach(['customer' => $user->stripe_customer_id]);

            //Make payment method as default for customer
            $this->stripeClient->customers->update($user->stripe_customer_id, [
                'invoice_settings' => [
                    'default_payment_method' => $payment_method_id,
                ],
            ]);

            $user_updates = [
                'card_type' => $payment_method->card->brand,
                'card_last_4' => $payment_method->card->last4,
                'card_expiry_date' => Carbon::create($payment_method->card->exp_year, $payment_method->card->exp_month),
            ];

            //User adding first payment method
            if (is_null($user->current_billing_date)) {
                $user_updates = array_merge($user_updates, [
                    'previous_billing_date' => null,
                    'current_billing_date' => Carbon::now()->addMonth(),
                    'user_inactivity_message' => null,
                    'is_active' => true,
                ]);
            }

            $user->update($user_updates);

            $this->toastMessageService->showToastMessage('success', 'Payment Method Saved Successfully');

            $this->customAuditingService->createCustomAudit($user, 'Successfully attached Stripe Payment method to customer');

        } catch (\Exception $e) {
            $this->toastMessageService->showToastMessage('error', 'Error in saving payment details');

            $this->customAuditingService->createCustomAudit($user, 'Exception when saving payment details', [
                'error' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
        }

        return redirect('/payments');
    }

    /**
     * @throws ApiErrorException
     */
    public function removePaymentMethod(User $user): void
    {
        $payment_methods = $this->stripeClient->paymentMethods->all([
            'customer' => $user->stripe_customer_id,
        ])->data;

        foreach ($payment_methods as $payment_method) {
            $payment_method->detach();
            $this->customAuditingService->createCustomAudit($user,
                'Stripe Payment method removed', [
                    ...$payment_method->toArray()
                ]
            );
        }
    }

    public function chargeCustomer(User $user, Invoice $invoice, PricingTier $pricing_tier, int $usage): bool
    {
        $paymentMethod = $this->stripeClient->paymentMethods->all([
            'customer' => $user->stripe_customer_id,
        ])->data[0];

        $data = [
            'amount' => ((($usage * $pricing_tier->price_per_api_call) + (double)SiteConfig::getConfig('payment_processing_fee')) * 100),
            'currency' => 'USD',
            'customer' => $user->stripe_customer_id,
            'description' => '',
            'metadata' => [
                'swear_stop_user_id' => $user->id,
            ],
            'confirm' => true,
            'off_session' => true,
            'payment_method' => $paymentMethod->id,
        ];

        $intent = $this->stripeClient->paymentIntents->create($data);

        $pending_statuses = ["requires_confirmation", "requires_action", "processing"];

        while (in_array($intent->status, $pending_statuses)) {
            sleep(1);
            $intent = $this->stripeClient->paymentIntents->retrieve($intent->id);
        }

        if ($intent->status == "succeeded") {
            $invoice->is_paid = true;
            $invoice->update();

            $this->customAuditingService->createCustomAudit($user,
                'Stripe Payment made successfully', [
                    ...$intent->toArray(),
                    'invoice_id' => $invoice->id
                ]
            );
            return true;
        } else {
            $invoice->is_paid = false;
            $invoice->update();

            $this->customAuditingService->createCustomAudit($user,
                'Stripe Payment failed', [
                    ...$intent->toArray(),
                    'invoice_id' => $invoice->id
                ]
            );
            return false;
        }
    }
}
