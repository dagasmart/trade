<?php

namespace DagaSmart\Trade\Http\Controllers;

use DagaSmart\BizAdmin\Controllers\AdminController;
use DagaSmart\Trade\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class PaymentController extends AdminController
{
    protected string $serviceName = PaymentService::class;

    public function index()
    {
        //判断扫描二维码的APP为 QQ
        if(str_contains($_SERVER['HTTP_USER_AGENT'], 'QQ')){
            $trade_channel = 'QQ';
            //判断扫描二维码的APP为 支付宝
        }ELSE IF(str_contains($_SERVER['HTTP_USER_AGENT'], 'Alipay')){
            $trade_channel = '支付宝';
            //判断扫描二维码的APP为 微信
        }ELSE IF(str_contains($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')){
            $trade_channel = '微信';
        } else {
            $trade_channel = null;
        }

        admin_abort($trade_channel);



    }
}
