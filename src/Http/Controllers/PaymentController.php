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
     * @return JsonResponse
     */
    public function detect(Request $request): JsonResponse
    {
        $data = [];
        $data['source'] = $request->source;
        $data['order_no'] = $request->order_no;
        //判断扫描二维码的APP为 QQ
        IF(str_contains($_SERVER['HTTP_USER_AGENT'], 'QQ')) {
            $trade_channel = 'qq';
        } ELSE IF (str_contains($_SERVER['HTTP_USER_AGENT'], 'Alipay')) {
            $trade_channel = 'alipay';
        } ELSE IF (str_contains($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            $trade_channel = 'wechat';
        } ELSE {
            admin_abort('无法正确识别扫码终端(仅支持微信、支付宝、抖音)');
        }
        $data['trade_channel'] = $trade_channel;

        $res = $this->service->detect($data);
        if (!$res) {
            return $this->response()->success($res,'成功');
        }
        return $this->response()->fail('失败');
    }


    /**
     * 识别扫码终端
     * @param Request $request
     * @return JsonResponse
     */
    public function order(Request $request): JsonResponse
    {
        return $this->response()->successMessage('2341234333222');
        return $this->service->order($request);

    }

}
