<?php

namespace App\Contracts;

use App\Models\Invoice;
use App\Models\PricingTier;
use App\Models\User;
use App\Services\ToastMessageService;
use Illuminate\Http\Request;
use Inertia\Response;

interface PaymentProviderContract
{
    /**
     * remove
     *
     * @param User $user
     */
    public function addCustomer(User $user): void;


    /**
     * render the payment method collection page
     *
     * @param Request $request
     * @return Response
     */
    public function renderPaymentMethodCollectionPage(Request $request): Response;

    /**
     * render the payment method collection page
     *
     * @param Request $request
     * @return Response
     */
    public function renderPaymentMethodUpdatePage(Request $request): Response;


    /**
     * save card details
     *
     * @param Request $request
     * @param ToastMessageService $toastMessageService
     * @return PaymentProviderContract
     */
    public function saveCard(Request $request): null|\Illuminate\Http\RedirectResponse;

    /**
     * remove
     *
     * @param User $user
     */
    public function removePaymentMethod(User $user): void;


    /**
     * remove
     *
     * @param User $user
     * @param Invoice $invoice
     * @param PricingTier $pricing_tier
     * @param int $usage
     * @return bool
     */
    public function chargeCustomer(User $user, Invoice $invoice, PricingTier $pricing_tier, int $usage): bool;
}
