<?php

namespace App\Contracts;

use App\Models\PricingTier;
use App\Models\User;
use App\Services\CustomAuditingService;
use App\Services\ToastMessageService;
use Illuminate\Http\Request;
use Inertia\Response;

interface PaymentProviderContract
{
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
     * @param CustomAuditingService $auditService
     * @return Response
     */
    public function renderPaymentMethodUpdatePage(Request $request, CustomAuditingService $auditService): Response;


    /**
     * save card details
     *
     * @param Request $request
     * @param ToastMessageService $toastMessageService
     * @param CustomAuditingService $auditService
     * @return PaymentProviderContract
     */
    public function saveCard(Request $request, ToastMessageService $toastMessageService, CustomAuditingService $auditService): void;

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
     * @param PricingTier $pricing_tier
     * @param int $usage
     * @return \Illuminate\Http\Client\Response
     */
    public function chargeCustomer(User $user, PricingTier $pricing_tier, int $usage): \Illuminate\Http\Client\Response;
}
