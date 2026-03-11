<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
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
class RefundService extends AdminService
{
    protected string $modelName = Order::class;


    /**
     * @param $id
     * @param $amount
     * @return bool
     */
    public function refundOrder($id, $amount): bool
    {
        $model = $this->getModel();
        $row = $model->query()->where(['id' => $id])->first();
        if ($row) {

            //退款金额累加
            $row->refund_amount += $amount;
            $is_bool = (bool) $row->trade_amount < $row->refund_amount;
            admin_abort_if($is_bool, '退款金额累计不能大于订单的支付金额 ' . $row->trade_amount);
            //全部退款
            if ($row->trade_amount == $row->refund_amount) {
                $row->trade_status = -1;
            }
            //部分退款
            if ($row->trade_amount > $row->refund_amount) {
                $row->trade_status = -2;
            }
            return admin_transaction(function () use ($row, $amount) {
                if ($row->save()) {
                    $log = [
                        'trade_id' => $row->id,
                        'order_no' => $row->order_no,
                        'trade_type' => in_array($row->trade_status, [0,1]) ? 1 : 2,
                        'trade_channel' => $row->trade_channel,
                        'trade_amount' => $amount,
                        'trade_status' => $row->trade_status,
                        'trade_code' => $row->trade_code,
                        'trade_result' => $row->trade_result,
                        'opera_type' => in_array($row->trade_status, [0,1]) ? 'user' : (admin_mer_id() ? 'mer' : 'plat'),
                        'opera_id' => admin_user_id(),
                        'opera' => collect(['user_id' => admin_user_id(), 'user_name' => admin_user_name()]),
                    ];
                    $model = new Record;
                    return $model->query()->insert($log);
                }
                return false;
            });
        }
        return false;
    }


}
