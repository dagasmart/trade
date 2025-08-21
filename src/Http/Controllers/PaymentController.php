<?php

namespace DagaSmart\Trade\Http\Controllers;

use DagaSmart\BizAdmin\Controllers\AdminController;
use DagaSmart\Trade\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


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
        IF(str_contains($_SERVER['HTTP_USER_AGENT'], 'QQ')) {
            $trade_channel = 'qq';
        } ELSE IF (str_contains($_SERVER['HTTP_USER_AGENT'], 'Alipay')) {
            $trade_channel = 'alipay';
        } ELSE IF (str_contains($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
            $trade_channel = 'wechat';
        } ELSE {
            $trade_channel = null;
        }
        admin_abort_if(!$trade_channel, '无法正确识别扫码终端(仅支持微信、支付宝、抖音)');
        $data['trade_channel'] = $trade_channel ?? null;
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
    public function order(Request $request): JsonResponse
    {
        return $this->response()->successMessage('2341234333222');
    }

    /**
     * 同步回调
     */
    public function return(Request $request): JsonResponse
    {
        $aliPublicKey = '你的支付宝公钥'; // 请替换为真实的公钥字符串，通常是从支付宝商户后台获取的
        if ($this->checkSign($request, $aliPublicKey)) {
            // 签名验证成功，可以处理业务逻辑，如订单状态的更新等
            return $this->response()->successMessage('支付成功');
        } else {
            // 签名验证失败，可能是数据被篡改，需要进一步处理或提示用户
            return $this->response()->successMessage('支付失败或数据被篡改');
        }

    }


}
