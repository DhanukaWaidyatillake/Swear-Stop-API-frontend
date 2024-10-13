<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentProviderManager;
use App\Models\PricingTier;
use App\Models\SiteConfig;
use App\Services\CustomAuditingService;
use App\Services\PaymentProcessingService;
use App\Services\ToastMessageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function load_manage_payments_page(Request $request, PaymentProcessingService $paymentProcessingService): \Inertia\Response
    {
        $usage_details = $paymentProcessingService->calculateCostAndUsageForCurrentBillingMonth($request->user());
        $usage_details['billing_date'] = Carbon::parse($request->user()->current_billing_date)->toFormattedDayDateString();
        return Inertia::render('ManagePayments')->with('usage_details', $usage_details);
    }


    public function get_pricing_structure(Request $request): \Illuminate\Database\Eloquent\Collection|array
    {
        return PricingTier::query()->select(['from', 'to', 'price_per_api_call'])->get();
    }


    public function load_payment_details_page(Request $request, PaymentProviderManager $paymentProviderManager): \Inertia\Response
    {
        return $paymentProviderManager->usingDefault()->renderPaymentMethodCollectionPage($request);
    }


    public function load_payment_details_update_page(Request $request, PaymentProviderManager $paymentProviderManager, CustomAuditingService $auditService): \Inertia\Response
    {
        return $paymentProviderManager->usingDefault()->renderPaymentMethodUpdatePage($request, $auditService);
    }


    public function card_saved_successfully(Request $request, PaymentProviderManager $paymentProviderManager, ToastMessageService $toastMessageService, CustomAuditingService $auditService): void
    {
        $paymentProviderManager->usingDefault()->saveCard($request, toastMessageService: $toastMessageService, auditService: $auditService);
    }


    public function card_saved_failed(Request $request, CustomAuditingService $auditService): void
    {
        $user = $request->user();
        $auditService->createCustomAudit($user, 'Payment Details Saving Failed', $request->all());
    }


    public function show_payment_method_removal_popup(Request $request, PaymentProcessingService $paymentProcessingService)
    {
        return $paymentProcessingService->calculateCostAndUsageForCurrentBillingMonth($request->user())['cost'];
    }


    public function remove_payment_method(Request $request, PaymentProcessingService $paymentProcessingService, CustomAuditingService $auditingService, ToastMessageService $toastMessageService, PaymentProviderManager $paymentProviderManager): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        //Charging the due amount from the customers card
        $isSuccessful = $paymentProcessingService->chargeCustomer($user, $paymentProviderManager);

        try {
            if ($isSuccessful) {
                //Cancelling the users subscription
                $paymentProviderManager->usingDefault()->removePaymentMethod($user);

                $auditingService->createCustomAudit($user, 'Due Amount charged successfully');

                $auditingService->createCustomAudit($user, 'Payment method before removal', [
                    'card_type' => $user->card_type,
                    'card_last_4' => $user->card_last_4,
                    'card_expiry_date' => $user->card_expiry_date,
                    'current_billing_date' => $user->current_billing_date,
                ]);

                //remove users card details
                $user->update([
                    'card_type' => null,
                    'card_last_4' => null,
                    'card_expiry_date' => null,
                    'current_billing_date' => null,
                    'is_active' => false,
                    'user_inactivity_message' => SiteConfig::getConfig('user_inactivity_message_for_no_card')
                ]);

                $auditingService->createCustomAudit($user, 'Payment method removed successfully');
                $toastMessageService->showToastMessage('success', 'Payment method removed successfully');
            } else {
                $auditingService->createCustomAudit($user, 'Failed to charge due amount');
                $toastMessageService->showToastMessage('error', 'Payment method removed failed');
            }
        } catch (\Exception $e) {
            $toastMessageService->showToastMessage('error', 'Payment method removal failed');
            $auditingService->createCustomAudit($user, 'Exception during payment method removal', [
                'message' => $e->getMessage(),
                'details' => $e->getTraceAsString(),
            ]);
        }

        return Redirect::route('load-payment-details-page');
    }
}
