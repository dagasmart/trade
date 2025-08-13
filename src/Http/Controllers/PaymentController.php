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


    public function handle(Request $request)
    {

    }

    /**
     * 识别扫码终端
     * @param Request $request
     * @return JsonResponse
     */
    public function detect(Request $request): JsonResponse
    {
        //判断扫描二维码的APP
        IF(str_contains($_SERVER['HTTP_USER_AGENT'], 'QQ')) {
            $request['trade_channel'] = 'qq';
        } ELSE IF (str_contains($_SERVER['HTTP_USER_AGENT'], 'Alipay')) {
            $request['trade_channel'] = 'alipay';
        } ELSE IF (str_contains($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            $request['trade_channel'] = 'wechat';
        } ELSE {
            $request['trade_channel'] = null;
        }

        return $this->service->detect($request);
    }


    /**
     * 识别扫码终端
     * @param Request $request
     * @return JsonResponse
     */
    public function order(Request $request): JsonResponse
    {
        return $this->service->order($request);

    }

}
