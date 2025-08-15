<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Payment;
use Yansongda\Pay\Pay;


/**
 * 支付服务类
 */
class PaymentService extends AdminService
{
    protected string $modelName = Payment::class;


    public function detect($request)
    {
        $config = settings()->get('payment');
        admin_abort_if($config['switch'], '未开启支付功能');
        Pay::config($config);
        if ($request['trade_channel'] == 'alipay') {
            return Pay::alipay()->h5([
                'out_trade_no' => time(),
                'total_amount' => '0.05',
                'subject' => 'dagasmart 测试 - 01',
                'quit_url' => 'https://bus.dagasmart.com',
            ]);
        } else if ($request['trade_channel'] == 'wechat') {
            return Pay::wechat()->h5([
                'out_trade_no' => time(),
                'total_amount' => '0.01',
                'subject' => 'yansongda 测试 - 01',
                'quit_url' => 'https://yansongda.cn',
            ]);
        } else {
            return false;
        }
    }

    public function order($request)
    {

    }


}
