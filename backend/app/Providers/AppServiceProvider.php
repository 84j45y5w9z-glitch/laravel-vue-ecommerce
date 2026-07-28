<?php

namespace App\Providers;

use App\Services\PaymentGateway\PaymentGatewayFactory;
use App\Services\PaymentGateway\PaymentGatewayInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * 註冊服務容器綁定。
     * 依 config('services.payment.default') 動態綁定要使用的金流服務（ecpay / newebpay），
     * Controller 只需注入 PaymentGatewayInterface，不需關心目前用的是哪一家金流。
     */
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, function () {
            return PaymentGatewayFactory::make(config('services.payment.default', 'ecpay'));
        });
    }

    public function boot(): void
    {
        //
    }
}
