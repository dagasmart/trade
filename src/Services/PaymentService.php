<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Payment;
use Yansongda\Artful\Exception\ContainerException;
use Yansongda\Pay\Pay;


/**
 * 支付服务类
 */
class PaymentService extends AdminService
{
    protected string $modelName = Payment::class;


    public function detect($request)
    {
        $config = admin_pay_config();
        $switch = $config['switch'] ?? null;
        admin_abort_if(!$switch, '未开启支付功能');

        $trade_channel = $request['trade_channel'] ?? null;
        $trade_channel_as = $this->getModel()->channelAs($trade_channel);
        admin_abort_if(!$trade_channel, $trade_channel_as . '支付通道不能为空：trade_channel');

        $trade_channel_config = $config[$trade_channel] ?? null;
        admin_abort_if(!$trade_channel_config, $trade_channel_as . '未配置');

        $trade_channel_config_default = $trade_channel_config['default'] ?? null;
        admin_abort_if(!$trade_channel_config_default, $trade_channel_as . '未配置默认参数组：defaut');

        $trade_channel_config_default_switch = $trade_channel_config_default['switch'] ?? null;
        admin_abort_if(!$trade_channel_config_default_switch, $trade_channel_as . '未开启支付通道');

        try {
            Pay::config($config);

            if ($trade_channel == 'alipay') {
                return Pay::alipay()->h5([
                    'out_trade_no' => time(),
                    'total_amount' => '0.05',
                    'subject' => 'dagasmart 测试 - 01',
                    'quit_url' => 'https://bus.dagasmart.com',
                ]);
            }

            if ($trade_channel == 'wechat') {
                return Pay::wechat()->h5([
                    'out_trade_no' => time(),
                    'total_amount' => '0.01',
                    'subject' => 'yansongda 测试 - 01',
                    'quit_url' => 'https://yansongda.cn',
                ]);
            }
        } catch (ContainerException $e) {
            admin_abort('交易异常，请稍候重试：' . $e->getMessage());
        }
    }

    public function order($request)
    {

    }


}
