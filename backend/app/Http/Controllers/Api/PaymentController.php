<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentGateway\PaymentGatewayFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * 前端呼叫此 API，取得要 POST 到金流頁面的表單欄位，
     * 前端收到後動態組一個 <form> 並 submit，導向綠界/藍新的付款頁。
     */
    public function pay(Request $request, int $orderId)
    {
        $data = $request->validate([
            'gateway' => ['nullable', 'in:ecpay,newebpay'],
        ]);

        $order = $request->user()->orders()->findOrFail($orderId);

        if ($order->status !== Order::STATUS_PENDING) {
            return response()->json(['message' => '此訂單無法重複付款'], 422);
        }

        $gatewayName = $data['gateway'] ?? config('services.payment.default', 'ecpay');
        $gateway = PaymentGatewayFactory::make($gatewayName);

        Payment::create([
            'order_id' => $order->id,
            'gateway' => $gatewayName,
            'amount' => $order->total_amount,
            'status' => 'pending',
        ]);

        return response()->json($gateway->createPaymentForm($order->load('items')));
    }

    public function notifyEcpay(Request $request)
    {
        return $this->handleNotify($request, 'ecpay');
    }

    public function notifyNewebpay(Request $request)
    {
        return $this->handleNotify($request, 'newebpay');
    }

    private function handleNotify(Request $request, string $gatewayName)
    {
        $payload = $request->all();
        $gateway = PaymentGatewayFactory::make($gatewayName);

        if (!$gateway->verifyCallback($payload)) {
            Log::warning("{$gatewayName} 付款通知簽章驗證失敗", $payload);

            return response('0|ErrorMsg', 400);
        }

        $orderNo = $payload['MerchantTradeNo'] ?? $payload['MerchantOrderNo'] ?? null;
        $order = Order::where('order_no', $orderNo)->first();

        if (!$order) {
            return response('0|OrderNotFound', 404);
        }

        DB::transaction(function () use ($gateway, $payload, $order, $gatewayName) {
            $success = $gateway->isPaymentSuccessful($payload);

            $order->payments()->where('gateway', $gatewayName)->latest()->first()?->update([
                'gateway_trade_no' => $gateway->extractTradeNo($payload),
                'status' => $success ? 'success' : 'failed',
                'raw_response' => $payload,
            ]);

            if ($success && $order->status === Order::STATUS_PENDING) {
                $order->update(['status' => Order::STATUS_PAID]);

                // 正式扣庫存：quantity 與 reserved 一起扣除
                foreach ($order->items as $item) {
                    $item->product->inventory()?->decrement('quantity', $item->quantity);
                    $item->product->inventory()?->decrement('reserved', $item->quantity);
                }
            }
        });

        // 綠界要求收到通知後回應純文字 "1|OK"，藍新則回應 JSON，這裡統一回傳文字以簡化模擬
        return response('1|OK');
    }
}
