<?php

namespace App\Http\Controllers;

use App\Models\PricingTier;
use App\Services\CostAndUsageCalculationService;
use App\Services\CustomAuditingService;
use App\Services\ToastMessageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Paddle\Cashier;

class PaymentController extends Controller
{
    public function load_manage_payments_page(Request $request, CostAndUsageCalculationService $costAndUsageCalculationService): \Inertia\Response
    {
        return Inertia::render('ManagePayments')->with(
            'usage_details',
            $costAndUsageCalculationService->calculateCostAndUsageForCurrentBillingMonth($request->user())
        );
    }

    public function card_saved_successfully(Request $request, ToastMessageService $toastMessageService, CustomAuditingService $auditService): void
    {
        $user = $request->user();

        try {
            $auditService->createCustomAudit($user, 'Paddle Checkout response', $request->all());

            $card_details = $request->get('data')['payment']['method_details']['card'] ?? null;
            $customer_id = $request->get('data')['customer']['id'] ?? null;
            if ($card_details && ($customer_id == $user->customer->paddle_id)) {
                //Updating customers card details on our end
                $user->update([
                    'card_type' => $card_details['type'],
                    'card_last_4' => $card_details['last4'],
                    'card_expiry_date' => Carbon::create($card_details['expiry_year'], $card_details['expiry_month'])
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

    public function card_saved_failed(Request $request, CustomAuditingService $auditService): void
    {
        $user = $request->user();
        $auditService->createCustomAudit($user, 'Payment Details Saving Failed', $request->all());
    }

    public function load_payment_details_page(Request $request): \Inertia\Response
    {
        if (!$request->user()->is_subscribed) {
            return Inertia::render('PaymentMethodCollectionPage');
        } else {
            abort(401, 'User already has payment method');
        }
    }

    public function load_payment_details_update_page(Request $request, CustomAuditingService $auditService): \Inertia\Response
    {
        //To update payment method we should pass an id of a payment method update transaction is passed to the payment method collection page
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

    public function get_pricing_structure(): \Illuminate\Database\Eloquent\Collection|array
    {
        return PricingTier::query()->select(['from', 'to', 'price_per_api_call'])->get();
    }
}
