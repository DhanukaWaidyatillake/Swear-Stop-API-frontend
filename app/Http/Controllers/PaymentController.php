<?php

namespace App\Http\Controllers;

use App\Services\CustomAuditingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Laravel\Paddle\Cashier;

class PaymentController extends Controller
{
    public function load_manage_payments_page()
    {
        return Inertia::render('ManagePayments');
    }

    public function card_saved_successfully(Request $request): void
    {
        $user = $request->user();
        $auditService = new CustomAuditingService();
        $auditService->createCustomAudit($user, 'Payment Details Saved', $request->all());

        $card_details = $request->get('data')['payment']['method_details']['card'] ?? null;
        if ($card_details) {
            $user->update([
                'card_type' => $card_details['type'],
                'card_last_4' => $card_details['last4'],
                'card_expiry_date' => Carbon::create($card_details['expiry_year'], $card_details['expiry_month'])
            ]);
        }

        Session::flash('toast',[
            'type' => 'success',
            'message' => 'Payment method saved successfully'
        ]);
    }

    public function card_saved_failed(Request $request): void
    {
        $user = $request->user();
        $auditService = new CustomAuditingService();
        $auditService->createCustomAudit($user, 'Payment Details Saving Failed', $request->all());
    }

    public function load_payment_details_page(Request $request): \Inertia\Response
    {
        if(!$request->user()->is_subscribed) {
            return Inertia::render('PaymentMethodCollectionPage');
        } else {
            abort(500,'User already has payment method');
        }
    }

    public function load_payment_details_update_page(Request $request): \Inertia\Response
    {
        //To update payment method we should pass an id of a payment method update transaction is passed to the payment method collection page

        $response = Cashier::api('GET', 'subscriptions/' . $request->user()->subscription()->asPaddleSubscription()['id'] . '/update-payment-method-transaction');

        return Inertia::render('PaymentMethodCollectionPage',[
            'txn_id' => $response->object()?->data?->id
        ]);
    }
}
