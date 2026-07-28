<?php

namespace App\Services\PaymentGateway;

use App\Models\Order;

/**
 * 綠界科技(ECPay) 金流串接（模擬版，使用官方公開的測試商店代號）
 * 正式上線請改用綠界官方 SDK 並替換為正式商店代號、HashKey、HashIV。
 * 文件：https://developers.ecpay.com.tw/
 */
class ECPayService implements PaymentGatewayInterface
{
    public function __construct(
        private readonly string $merchantId,
        private readonly string $hashKey,
        private readonly string $hashIv,
        private readonly string $env = 'stage',
    ) {
    }

    public function name(): string
    {
        return 'ecpay';
    }

    public function createPaymentForm(Order $order): array
    {
        $params = [
            'MerchantID' => $this->merchantId,
            'MerchantTradeNo' => $order->order_no,
            'MerchantTradeDate' => now()->format('Y/m/d H:i:s'),
            'PaymentType' => 'aio',
            'TotalAmount' => $order->total_amount,
            'TradeDesc' => 'Shop Order ' . $order->order_no,
            'ItemName' => $order->items->pluck('product_name')->implode('#'),
            'ReturnURL' => config('app.url') . '/api/payments/ecpay/notify',
            'ClientBackURL' => rtrim(env('FRONTEND_URL', config('app.url')), '/') . '/orders/' . $order->id,
            'ChoosePayment' => 'Credit',
            'EncryptType' => 1,
        ];

        $params['CheckMacValue'] = $this->generateCheckMacValue($params);

        $endpoint = $this->env === 'production'
            ? 'https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5'
            : 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5';

        return [
            'action' => $endpoint,
            'method' => 'POST',
            'fields' => $params,
        ];
    }

    public function verifyCallback(array $payload): bool
    {
        $received = $payload['CheckMacValue'] ?? null;
        unset($payload['CheckMacValue']);

        return $received !== null && hash_equals($this->generateCheckMacValue($payload), $received);
    }

    public function extractTradeNo(array $payload): ?string
    {
        return $payload['TradeNo'] ?? null;
    }

    public function isPaymentSuccessful(array $payload): bool
    {
        return ($payload['RtnCode'] ?? null) === '1';
    }

    /**
     * ECPay CheckMacValue 產生規則：
     * 1. 參數依 A-Z 排序 2. 前後加上 HashKey / HashIV 3. urlencode 後轉小寫 4. SHA256 後轉大寫
     */
    private function generateCheckMacValue(array $params): string
    {
        ksort($params);
        $raw = 'HashKey=' . $this->hashKey . '&' . urldecode(http_build_query($params)) . '&HashIV=' . $this->hashIv;
        $raw = strtolower(urlencode($raw));

        return strtoupper(hash('sha256', $raw));
    }
}
