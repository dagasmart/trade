<?php

use DagaSmart\BizAdmin\Middleware\Authenticate;
use DagaSmart\BizAdmin\Middleware\Permission;
use Illuminate\Routing\Router;
use DagaSmart\Trade\Http\Controllers;
use Illuminate\Support\Facades\Route;


//需登录与鉴权
Route::group([
    'domain'     => config('admin.route.domain'),
    'prefix'     => 'trade',
    'middleware' => [Authenticate::class],
], function (Router $router) {
    //交易流水
    $router->resource('record', Controllers\RecordController::class);
    //账单结算
    $router->resource('settle', Controllers\SettleController::class);
    //交易分析
    $router->resource('stat', Controllers\StatController::class);
    //支付设置
    $router->resource('settings', Controllers\SettingController::class);
    //识别终端/生成订单
    Route::get('payment/detect/{source}/{order_no}', [Controllers\PaymentController::class, 'detect'])->withoutMiddleware([Authenticate::class, Permission::class]);
    //订单付款
    Route::get('payment/order/{order_no}', [Controllers\PaymentController::class, 'order'])->withoutMiddleware([Authenticate::class, Permission::class]);
});

//免登录无限制
Route::group([
    'domain'     => config('admin.route.domain'),
    'prefix'     => 'trade',
], function (Router $router) {
    $router->get('_iconify_search', [\DagaSmart\BizAdmin\Controllers\IndexController::class, 'iconifySearch']);

    Route::get('trade', [Controllers\TradeController::class, 'index']);

});
