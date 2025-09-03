<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Payment;
use DagaSmart\Trade\Models\Order;
use DagaSmart\Trade\Models\Record;
use ErrorException;
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
     * @return ResponseInterface|true|Rocket|Collection
     * @throws ErrorException
     */
    public function payOrder($data): true|Collection|ResponseInterface|Rocket
    {
        $source = $data['source'] ?? null;
        if (!$source) {
            throw new ErrorException('订单来源不能为空：source');
        }
        $module = $data['module'] ?? null;
        $mer_id = $data['mer_id'] ?? null;
        $user_id = $data['user_id'] ?? null;
        $is_plat = $data['is_plat'] ?? in_array($source, $this->modelName->plat);
        //非平台订单时，商户必须存在
        if (!$is_plat) {
            if (!$module) {
                throw new ErrorException('模块不能为空：module');
            }
            if (!$mer_id) {
                throw new ErrorException('商户不能为空：mer_id');
            }
        }
        $cfg = [];
        $cfg['module'] = $module;
        $cfg['mer_id'] = $mer_id;
        $cfg['is_plat'] = $is_plat;
        $config = trade_pay_config($cfg);

        $switch = $config['switch'] ?? null;
        if (!$switch) {
            throw new ErrorException('未开启支付功能');
        }

        $order_id = $data['order_id'] ?? null;

        $base_order_no = $data['order_no'] ?? null;
        if (!$base_order_no) {
            throw new ErrorException('订单号不能为空：order_no');
        }

        $trade_channel = $data['trade_channel'] ?? null;
        $trade_channel_as = $this->getModel()->channelAs($trade_channel);
        if (!$trade_channel) {
            throw new ErrorException($trade_channel_as . '支付通道不能为空：trade_channel');
        }

        $trade_channel_config = $config[$trade_channel] ?? null;
        if (!$trade_channel_config) {
            throw new ErrorException($trade_channel_as . '未配置');
        }

        $trade_channel_config_default = $trade_channel_config['default'] ?? null;
        if (!$trade_channel_config_default) {
            throw new ErrorException($trade_channel_as . '未配置默认参数组：defaut');
        }

        $pay_amount = $data['pay_amount'] ?? 0;
        $trade_channel_config_default_switch = $trade_channel_config_default['switch'] ?? null;
        if (!$trade_channel_config_default_switch) {
            if ($pay_amount <= 0) {
                throw new ErrorException($trade_channel_as . '金额无效: amount=' . $pay_amount);
            }else {
                throw new ErrorException($trade_channel_as . '未开启支付通道');
            }
        }

        //订单微秒时间戳
        $lat = $data['lat'] ?? null;
        //订单前缀
        $prefix = $is_plat ? $source : null;
        //交易订单号
        $trade_order_sn = trade_order_sn($lat, $prefix);
        //更新或新增订单数据
        $model = new Order;
        $record = $model->query()->updateOrCreate(
            // 查找条件，如果找不到，则按这些条件创建新记录，并更新这些字段的值
            [
//                'order_id' => $order_id,
                'order_no' => $trade_order_sn,
//                'base_order_no' => $base_order_no,
//                'order_source' => $source,
//                'trade_channel' => $trade_channel,
//                'trade_status' => 0,
//                'is_plat' => $is_plat,
//                'module' => $module,
//                'mer_id' => $mer_id,
//                'payer_id' => $user_id,
            ],
            // 新记录的默认值或需要更新的字段和值
            [
                'order_id' => $order_id,
                'order_no' => $trade_order_sn,
                'base_order_no' => $base_order_no,
                'order_source' => $source,
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

        //记录流水
        if ($record) {
            $log = [
                'trade_id' => $record->id,
                'order_no' => $record->order_no,
                'trade_type' => in_array($record->trade_status, [0,1]) ? 1 : 2,
                'trade_channel' => $record->trade_channel,
                'trade_amount' => $record->trade_amount,
                'trade_status' => $record->trade_status,
                'trade_code' => $record->trade_code,
                'trade_result' => $record->trade_result,
                'opera_type' => in_array($record->trade_status, [0,1]) ? 'user' : (admin_mer_id() ? 'mer' : 'plat'),
                'opera_id' => $record->payer_id,
                'opera' => $record->payer,
            ];
            $model = new Record;
            $model->query()->insert($log);
        }

        try {
            Pay::config($config);
            //支付宝
            if ($trade_channel == 'alipay') {
                return Pay::alipay()->h5([
                    'out_trade_no' => $record->order_no,
                    'total_amount' => 0.05,//$record->trade_amount,
                    'subject' => $this->getModel()->source($record->order_source),
                    'quit_url' => 'https://bus.dagasmart.com',
                ]);
            }
            //微信
            if ($trade_channel == 'wechat') {
                return Pay::wechat()->h5([
                    'out_trade_no' => $record->order_no,
                    'total_amount' => 0.05,//$record->trade_amount,
                    'subject' => $this->getModel()->source($record->order_source),
                    'quit_url' => 'https://yansongda.cn',
                ]);
            }
            //抖音
            if ($trade_channel == 'douyin') {
                return Pay::wechat()->h5([
                    'out_trade_no' => $record->order_no,
                    'total_amount' => 0.05,//$record->trade_amount,
                    'subject' => $this->getModel()->source($record->order_source),
                    'quit_url' => 'https://yansongda.cn',
                ]);
            }
            //银联
            if ($trade_channel == 'unipay') {
                return Pay::wechat()->h5([
                    'out_trade_no' => $record->order_no,
                    'total_amount' => 0.05,//$record->trade_amount,
                    'subject' => $this->getModel()->source($record->order_source),
                    'quit_url' => 'https://yansongda.cn',
                ]);
            }
            return true;
        } catch (ContainerException $e) {
            throw new ErrorException('交易异常，请稍候重试：' . $e->getMessage());
        }
    }


}
