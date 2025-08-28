<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Payment;
use DagaSmart\Trade\Models\TradeOrder;


/**
 * 支付同步回调类
 */
class ReturnService extends AdminService
{

    public function paySave($data)
    {
        $model = new TradeOrder;
        $row = $model->query()
            ->where(['order_no' => $data->out_trade_no])
            ->where(['trade_status' => 0])
            ->first();
        $row->trade_no = $data->trade_no;
        $row->trade_amount = $data->total_amount;
        $row->trade_status = 1; //支付成功
        $row->trade_time = $data->timestamp;
        return $row->save();
    }


}
