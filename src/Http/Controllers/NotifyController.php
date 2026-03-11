<?php

namespace DagaSmart\Trade\Http\Controllers;

use DagaSmart\BizAdmin\Controllers\AdminController;
use DagaSmart\Trade\Services\NotifyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class NotifyController extends AdminController
{

    protected string $serviceName = NotifyService::class;


    public function __construct(NotifyService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function alipay(Request $request)
    {
        dump($request);
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
