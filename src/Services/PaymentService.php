<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Payment;
use DagaSmart\Trade\Models\TradeOrder;
use Psr\Http\Message\ResponseInterface;
use Yansongda\Artful\Exception\ContainerException;
use Yansongda\Artful\Rocket;
use Yansongda\Pay\Pay;
use Yansongda\Supports\Collection;


/**
 * 支付服务类
 */
class PaymentService extends AdminService
{
    protected string $modelName = Payment::class;


    /**
     * @param $data
     */
    public function payOrder($data)
    {
        $source = $data['source'] ?? null;
        admin_abort_if(!$source, '订单来源不能为空：source');
        $module = $data['module'] ?? null;
        $mer_id = $data['mer_id'] ?? null;
        $user_id = $data['user_id'] ?? null;
        $is_plat = $data['is_plat'] ?? in_array($source, $this->modelName->plat);
        $cfg = [];
        $cfg['module'] = $module;
        $cfg['mer_id'] = $mer_id;
        $cfg['is_plat'] = $is_plat;

        $config = trade_pay_config($cfg);
        $switch = $config['switch'] ?? null;
        admin_abort_if(!$switch, '未开启支付功能');

        //非平台订单时，商户必须存在
        if (!$is_plat) {
            admin_abort_if(!$module, '模块不能为空：module');
            admin_abort_if(!$mer_id, '商户不能为空：mer_id');
        }

        $order_id = $data['order_id'] ?? null;

        $base_order_no = $data['order_no'] ?? null;
        admin_abort_if(!$base_order_no, '订单号不能为空：order_no');

        $trade_channel = $data['trade_channel'] ?? null;
        $trade_channel_as = $this->getModel()->channelAs($trade_channel);
        admin_abort_if(!$trade_channel, $trade_channel_as . '支付通道不能为空：trade_channel');

        $trade_channel_config = $config[$trade_channel] ?? null;
        admin_abort_if(!$trade_channel_config, $trade_channel_as . '未配置');

        $trade_channel_config_default = $trade_channel_config['default'] ?? null;
        admin_abort_if(!$trade_channel_config_default, $trade_channel_as . '未配置默认参数组：defaut');

        $trade_channel_config_default_switch = $trade_channel_config_default['switch'] ?? null;
        admin_abort_if(!$trade_channel_config_default_switch, $trade_channel_as . '未开启支付通道');

        $pay_amount = $data['pay_amount'] ?? 0;
        if($pay_amount <= 0) {
            admin_abort_if(!$trade_channel_config_default_switch, $trade_channel_as . '金额无效: amount=' . $pay_amount);
        }
        //订单微秒时间戳
        $lat = $data['lat'] ?? null;
        //订单前缀
        $prefix = $is_plat ? $source : null;
        //交易订单号
        $trade_order_sn = trade_order_sn($lat, $prefix);
        //更新或新增订单数据
        $model = new TradeOrder;
        $record = $model->query()->updateOrCreate(
            // 查找条件，如果找不到，则按这些条件创建新记录，并更新这些字段的值
            [
                'order_id' => $order_id,
                'base_order_no' => $base_order_no,
                'order_source' => $source,
                'trade_channel' => $trade_channel,
                'trade_status' => 0,
                'is_plat' => $is_plat,
                'module' => $module,
                'mer_id' => $mer_id,
                'payer_id' => $user_id,
            ],
            // 新记录的默认值或需要更新的字段和值
            [
                'order_id' => $order_id,
                'order_no' => $trade_order_sn,
                'base_order_no' => $base_order_no,
                'order_source' => $source,
                'trade_type' => 1,
                'trade_channel' => $trade_channel,
                'trade_amount' => $pay_amount,
                'is_plat' => $is_plat,
                'module' => $module,
                'mer_id' => $mer_id,
                'payer_id' => 1,
                'payer' => json_encode(['user_id' => 1, 'user_name' => 'admin'],JSON_UNESCAPED_UNICODE)
            ]
        );

        $record = (object) $record->toArray();

        try {

            Pay::config($config);

            if ($trade_channel == 'alipay') {
                return Pay::alipay()->h5([
                    'out_trade_no' => $record->order_no,
                    'total_amount' => 0.05,//$record->trade_amount,
                    'subject' => $this->getModel()->source($record->order_source),
                    'quit_url' => 'https://bus.dagasmart.com',
                ]);
            }

            if ($trade_channel == 'wechat') {
                return Pay::wechat()->h5([
                    'out_trade_no' => $record->order_no,
                    'total_amount' => 0.05,//$record->trade_amount,
                    'subject' => $this->getModel()->source($record->order_source),
                    'quit_url' => 'https://yansongda.cn',
                ]);
            }
            return true;
        } catch (ContainerException $e) {
            admin_abort('交易异常，请稍候重试：' . $e->getMessage());
        }
    }

    public function paying($data)
    {

    }


}
