<?php

return [
    'ecpay' => [
        'merchant_id' => env('ECPAY_MERCHANT_ID'),
        'hash_key' => env('ECPAY_HASH_KEY'),
        'hash_iv' => env('ECPAY_HASH_IV'),
        'env' => env('ECPAY_ENV', 'stage'),
    ],

    'newebpay' => [
        'merchant_id' => env('NEWEBPAY_MERCHANT_ID'),
        'hash_key' => env('NEWEBPAY_HASH_KEY'),
        'hash_iv' => env('NEWEBPAY_HASH_IV'),
        'env' => env('NEWEBPAY_ENV', 'stage'),
    ],

    'payment' => [
        'default' => env('PAYMENT_DEFAULT_GATEWAY', 'ecpay'),
    ],
];
