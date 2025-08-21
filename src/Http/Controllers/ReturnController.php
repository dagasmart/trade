<?php

namespace DagaSmart\Trade\Http\Controllers;

use Closure;
use DagaSmart\BizAdmin\Controllers\AdminController;
use DagaSmart\Trade\Services\ReturnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ReturnController extends AdminController
{

    protected string $serviceName = ReturnService::class;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @return mixed
     */
    public function handle(Request $request): mixed
    {
        //支付通道，alipay|wechat|unipay|douyin
        $channel = $request['channel'];
        return $this->$channel($request);
    }

    /**
     * 支付宝
     * @param Request $request
     * @return JsonResource|JsonResponse
     */
    public function alipay(Request $request)
    {
        dump($request->query);

        dump(admin_current_module());

        dump(settings()->pay('payment'));die;

        $config = trade_pay_config($data);

        $aliPublicKey = '你的支付宝公钥'; // 请替换为真实的公钥字符串，通常是从支付宝商户后台获取的
        if ($this->checkSign($request, $aliPublicKey)) {
            // 签名验证成功，可以处理业务逻辑，如订单状态的更新等
            return $this->response()->success([],'支付成功');
        } else {
            // 签名验证失败，可能是数据被篡改，需要进一步处理或提示用户
            return $this->response()->fail('支付失败或数据被篡改');
        }



    }

    public function wechat(Request $request)
    {
        dump(3333);
    }


    /**
     * 支付回调验签
     * @param $params
     * @param $aliPublicKey
     * @param string $charset
     * @return bool
     */
    public function checkSign($params, $aliPublicKey, string $charset = 'UTF-8'): bool
    {
        ksort($params);
        $signStr = "";
        foreach ($params as $k => $v) {
            if (strtolower($k) != "sign" && $v != "" && !is_array($v)) {
                $signStr .= "$k=$v&";
            }
        }
        $signStr = rtrim($signStr, '&');
        // 加载支付宝的公钥
        $pubKey = openssl_pkey_get_public($aliPublicKey);
        // 使用openssl_verify验证签名
        $result = (bool) openssl_verify($signStr, base64_decode($params['sign']), $pubKey, OPENSSL_ALGO_SHA256);
        openssl_free_key($pubKey); // 释放资源
        return $result;
    }

}
