<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CustomAuditingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Paddle\Cashier;
use Laravel\Paddle\Exceptions\PaddleException;

class PaymentController extends Controller
{
    public function card_saved_successfully(Request $request): void
    {
        $user = $request->user();
        $auditService = new CustomAuditingService();
        $auditService->createCustomAudit($user, 'Payment Details Saved', $request->all());

        $card_details = $request->get('data')['payment']['method_details']['card'] ?? null;
        if($card_details) {
            $user->update([
                'card_type' => $card_details['type'],
                'card_last_4' => $card_details['last4'],
                'card_expiry_date' => Carbon::create($card_details['expiry_year'],$card_details['expiry_month'])
            ]);
        }
    }

    public function card_saved_failed(Request $request): void
    {
        $user = $request->user();
        $auditService = new CustomAuditingService();
        $auditService->createCustomAudit($user, 'Payment Details Saving Failed', $request->all());
    }

    /**
     * @throws PaddleException
     */
    public function pre_payment_method_change(Request $request)
    {
        $response = Cashier::api('GET','subscriptions/'.$request->user()->subscription()->asPaddleSubscription()['id'].'/update-payment-method-transaction');
        return $response->object()?->data?->id;
    }
}
