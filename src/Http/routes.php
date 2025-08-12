<?php

use Illuminate\Routing\Router;
use DagaSmart\Trade\Http\Controllers;
use Illuminate\Support\Facades\Route;


//需登录与鉴权
Route::group([
    'domain'     => config('admin.route.domain'),
    'prefix'     => 'trade',
    //'middleware' => 'trade',
], function (Router $router) {
    //交易流水
    $router->resource('record', Controllers\RecordController::class);
    //账单结算
    $router->resource('settle', Controllers\SettleController::class);
    //交易分析
    $router->resource('stat', Controllers\StatController::class);
    //支付设置
    $router->resource('settings', Controllers\SettingController::class);
});

//免登录无限制
Route::group([
    'domain'     => config('admin.route.domain'),
    'prefix'     => 'trade',
], function (Router $router) {
    $router->get('_iconify_search', [\DagaSmart\BizAdmin\Controllers\IndexController::class, 'iconifySearch']);

    Route::get('trade', [Controllers\TradeController::class, 'index']);

});
