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
    $router->resource('settings', Controllers\SettingController::class);
    Route::resource('stat', Controllers\StatController::class);
});

//免登录无限制
Route::group([
    'domain'     => config('admin.route.domain'),
    'prefix'     => 'trade',
], function (Router $router) {
    $router->get('_iconify_search', [\DagaSmart\BizAdmin\Controllers\IndexController::class, 'iconifySearch']);

    Route::get('trade', [Controllers\TradeController::class, 'index']);

});
