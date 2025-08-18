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


    /**
     * 识别扫码终端
     * @param Request $request
     * @return mixed
     */
    public function detect(Request $request): mixed
    {
        $data = [];
        $data['source'] = $request->source;
        $data['order_no'] = $request->order_no;
        //判断扫描二维码的APP为 QQ
//        IF(str_contains($_SERVER['HTTP_USER_AGENT'], 'QQ')) {
//            $trade_channel = 'qq';
//        } ELSE IF (str_contains($_SERVER['HTTP_USER_AGENT'], 'Alipay')) {
//            $trade_channel = 'alipay';
//        } ELSE IF (str_contains($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
//            $trade_channel = 'wechat';
//        } ELSE {
//            admin_abort('无法正确识别扫码终端(仅支持微信、支付宝、抖音)');
//        }
        $data['trade_channel'] = $trade_channel ?? 'alipay';

        return $this->payOrder($data);
    }

    /**
     * 创建订单
     * 进行付款
     * @param $data
     * @return mixed
     */
    public function payOrder($data): mixed
    {
        return $this->service->payOrder($data);
    }


    /**
     * 识别扫码终端
     * @param Request $request
     * @return JsonResponse
     */
    public function order2(Request $request): JsonResponse
    {
        return $this->response()->successMessage('2341234333222');
    }

}
