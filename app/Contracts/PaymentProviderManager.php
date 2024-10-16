<?php

namespace App\Contracts;

use App\Services\CustomAuditingService;
use App\Services\ToastMessageService;

class PaymentProviderManager
{
    /**
     * The array of resolved payment providers.
     *
     * @var array
     */
    protected array $payment_providers = [];

    private CustomAuditingService $customAuditingService;

    private ToastMessageService $toastMessageService;

    public function __construct(CustomAuditingService $customAuditingService, ToastMessageService $toastMessageService)
    {
        $this->customAuditingService = $customAuditingService;
        $this->toastMessageService = $toastMessageService;
    }

    /**
     * Get a payment gateway instance.
     *
     * @param string|null $name
     * @return PaymentProviderContract
     */
    public function using(?string $name): PaymentProviderContract
    {
        return $this->get($name);
    }

    /**
     * Get a payment gateway instance.
     *
     * @return PaymentProviderContract
     */
    public function usingDefault(): PaymentProviderContract
    {
        return $this->using(config('app.active_payment_provider'));
    }


    /**
     * Attempt to get the payment provider from the local cache.
     *
     * @param string $name
     * @return PaymentProviderContract
     * @throws \Exception
     */
    protected function get(string $name): PaymentProviderContract
    {
        return $this->payment_providers[$name] ?? $this->resolve($name);
    }

    /**
     * Resolve the given payment provider
     *
     * @param string $name Name of gateway to use for the payment provider
     * @return PaymentProviderContract
     *
     * @throws \Exception
     * */
    protected function resolve($name): PaymentProviderContract
    {
        $paymentProviderMethod = 'create' . ucfirst($name) . 'PaymentProvider';

        $this->payment_providers[$name] = $this->{$paymentProviderMethod}();

        return $this->payment_providers[$name];
    }

    /**
     * Create an instance of stripe payment provider.
     *
     * @return StripePaymentProvider
     */
    public function createStripePaymentProvider(): StripePaymentProvider
    {
        return new StripePaymentProvider($this->customAuditingService, $this->toastMessageService);
    }

    /**
     * Create an instance of paddle payment provider.
     *
     * @return PaddlePaymentProvider
     */
    public function createPaddlePaymentProvider(): PaddlePaymentProvider
    {
        return new PaddlePaymentProvider($this->customAuditingService, $this->toastMessageService);
    }
}
