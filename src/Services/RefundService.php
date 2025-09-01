<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Order;
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
     * @param $use_amount
     * @return bool
     */
    public function refundOrder($id, $use_amount): bool
    {
        $model = $this->getModel();
        $row = $model->query()->where(['id' => $id])->first();
        if ($row) {
            //退款金额累加
            $row->refund_amount += $use_amount;
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
            return $row->save();
        }
        return false;
    }


}
