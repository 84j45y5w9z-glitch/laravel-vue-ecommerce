<?php

namespace App\Services\PaymentGateway;

use InvalidArgumentException;

class PaymentGatewayFactory
{
    public static function make(string $gateway): PaymentGatewayInterface
    {
        return match ($gateway) {
            'ecpay' => new ECPayService(
                config('services.ecpay.merchant_id'),
                config('services.ecpay.hash_key'),
                config('services.ecpay.hash_iv'),
                config('services.ecpay.env', 'stage'),
            ),
            'newebpay' => new NewebPayService(
                config('services.newebpay.merchant_id'),
                config('services.newebpay.hash_key'),
                config('services.newebpay.hash_iv'),
                config('services.newebpay.env', 'stage'),
            ),
            default => throw new InvalidArgumentException("Unsupported payment gateway: {$gateway}"),
        };
    }
}
