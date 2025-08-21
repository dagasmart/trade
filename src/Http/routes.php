<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use DagaSmart\Trade\Http\Controllers;
use DagaSmart\BizAdmin\Middleware\Permission;
use DagaSmart\BizAdmin\Middleware\Authenticate;


//需登录与鉴权
Route::group([
    'prefix'     => 'trade',
], function (Router $router) {
    //交易流水
    $router->resource('record', Controllers\RecordController::class);
    //账单结算
    $router->resource('settle', Controllers\SettleController::class);
    //交易分析
    $router->resource('stat', Controllers\StatController::class);
    //支付设置
    $router->resource('settings', Controllers\SettingController::class);
    //订单付款
    $router->get('payment/order/{order_no}', [Controllers\PaymentController::class, 'order']);
});

//免登录无限制
Route::group([
    'prefix'     => 'trade',
], function (Router $router) {
    //识别终端/生成订单
    $router->get('payment/detect/{source}/{order_no}', [Controllers\PaymentController::class, 'detect'])->withoutMiddleware([Authenticate::class, Permission::class]);
    $router->get('payment/order/{source}/{order_no}', [Controllers\PaymentController::class, 'order'])->withoutMiddleware([Authenticate::class, Permission::class]);
    $router->get('payment/return', [Controllers\PaymentController::class, 'return'])->withoutMiddleware([Authenticate::class, Permission::class]);

    //支付同步回调
    $router->get('return/{channel}', [Controllers\ReturnController::class, 'handle'])->withoutMiddleware([Authenticate::class, Permission::class]);
    //支付异步回调
    $router->get('notify/{channel}', [Controllers\NotifyController::class, 'handle'])->withoutMiddleware([Authenticate::class, Permission::class]);

});
