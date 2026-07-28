<?php

namespace App\Services\PaymentGateway;

use App\Models\Order;

/**
 * 藍新金流(NewebPay) 串接（模擬版）
 * 正式串接請參考藍新官方文件替換 AES 加密參數與 API 端點。
 * 文件：https://www.newebpay.com/website/Page/content/download_api
 */
class NewebPayService implements PaymentGatewayInterface
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
        return 'newebpay';
    }

    public function createPaymentForm(Order $order): array
    {
        $tradeInfoPlain = http_build_query([
            'MerchantID' => $this->merchantId,
            'RespondType' => 'JSON',
            'TimeStamp' => now()->timestamp,
            'Version' => '2.0',
            'MerchantOrderNo' => $order->order_no,
            'Amt' => $order->total_amount,
            'ItemDesc' => 'Shop Order ' . $order->order_no,
            'ReturnURL' => rtrim(env('FRONTEND_URL', config('app.url')), '/') . '/orders/' . $order->id,
            'NotifyURL' => config('app.url') . '/api/payments/newebpay/notify',
        ]);

        $tradeInfo = $this->encryptTradeInfo($tradeInfoPlain);
        $tradeSha = strtoupper(hash('sha256', "HashKey={$this->hashKey}&{$tradeInfo}&HashIV={$this->hashIv}"));

        $endpoint = $this->env === 'production'
            ? 'https://core.newebpay.com/MPG/mpg_gateway'
            : 'https://ccore.newebpay.com/MPG/mpg_gateway';

        return [
            'action' => $endpoint,
            'method' => 'POST',
            'fields' => [
                'MerchantID' => $this->merchantId,
                'TradeInfo' => $tradeInfo,
                'TradeSha' => $tradeSha,
                'Version' => '2.0',
            ],
        ];
    }

    public function verifyCallback(array $payload): bool
    {
        $tradeInfo = $payload['TradeInfo'] ?? null;
        $tradeSha = $payload['TradeSha'] ?? null;

        if (!$tradeInfo || !$tradeSha) {
            return false;
        }

        $expected = strtoupper(hash('sha256', "HashKey={$this->hashKey}&{$tradeInfo}&HashIV={$this->hashIv}"));

        return hash_equals($expected, $tradeSha);
    }

    public function extractTradeNo(array $payload): ?string
    {
        $decoded = $this->decodeTradeInfo($payload['TradeInfo'] ?? '');

        return $decoded['Result']['TradeNo'] ?? null;
    }

    public function isPaymentSuccessful(array $payload): bool
    {
        $decoded = $this->decodeTradeInfo($payload['TradeInfo'] ?? '');

        return ($decoded['Status'] ?? null) === 'SUCCESS';
    }

    private function encryptTradeInfo(string $plain): string
    {
        $key = $this->hashKey;
        $iv = $this->hashIv;
        $encrypted = openssl_encrypt(
            $this->pkcs7Pad($plain),
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );

        return bin2hex($encrypted);
    }

    private function decodeTradeInfo(string $hex): array
    {
        if ($hex === '') {
            return [];
        }

        $decrypted = openssl_decrypt(
            hex2bin($hex),
            'AES-256-CBC',
            $this->hashKey,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $this->hashIv
        );

        $decrypted = rtrim($decrypted, "\x00..\x1F");

        return json_decode($decrypted, true) ?? [];
    }

    private function pkcs7Pad(string $data, int $blockSize = 32): string
    {
        $pad = $blockSize - (strlen($data) % $blockSize);

        return $data . str_repeat(chr($pad), $pad);
    }
}
