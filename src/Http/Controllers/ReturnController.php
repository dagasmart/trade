<?php

namespace DagaSmart\Trade\Http\Controllers;

use DagaSmart\BizAdmin\Controllers\AdminController;
use DagaSmart\Trade\Services\ReturnService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Psr\Http\Message\ResponseInterface;
use Yansongda\Artful\Exception\ContainerException;
use Yansongda\Artful\Exception\InvalidParamsException;
use Yansongda\Pay\Pay;


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
     * @return ResponseInterface
     * @throws ContainerException|InvalidParamsException
     */
    public function alipay(Request $request): ResponseInterface
    {
//        dump($request->toArray());
        $config = trade_pay_config();
        Pay::config($config);

        $alipay = Pay::alipay();

        try {
            $data = $alipay->callback(); //回调验签
            if ($alipay->success() == 'success') {
                $this->service->paySave($data);
            }

            // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况
            //Log::info('回调数据' . $data);
        } catch (Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success();








        $data = $request->toArray();

        $config = trade_pay_config();
        $alipay_public_cert_path = $config['alipay']['default']['alipay_public_cert_path'];
        $aliPublicKey = $alipay_public_cert_path; // 你的支付宝公钥，请替换为真实的公钥字符串，通常是从支付宝商户后台获取的
        if ($this->checkSign($data, $aliPublicKey)) {
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


}
