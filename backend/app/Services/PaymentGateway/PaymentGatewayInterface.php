<?php

namespace App\Services\PaymentGateway;

use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * 產生導向金流付款頁面所需的表單參數（商店代號、檢查碼...等）
     * 前端收到這組資料後，用 form POST 導向金流頁面
     */
    public function createPaymentForm(Order $order): array;

    /**
     * 驗證金流平台回傳的付款結果通知（Server 端 Notify / Webhook），
     * 回傳 true 代表簽章驗證通過
     */
    public function verifyCallback(array $payload): bool;

    /** 從回傳資料中取出金流端的交易編號 */
    public function extractTradeNo(array $payload): ?string;

    /** 判斷這筆通知是否代表付款成功 */
    public function isPaymentSuccessful(array $payload): bool;

    /** 這個 gateway 的識別字串，例如 ecpay / newebpay */
    public function name(): string;
}
