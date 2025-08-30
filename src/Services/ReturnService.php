<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Payment;
use DagaSmart\Trade\Models\TradeOrder;
use ErrorException;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;


/**
 * 支付同步回调类
 */
class ReturnService extends AdminService
{

    /**
     * @throws ErrorException
     */
    public function paySave($data)
    {
        throw new ErrorException('我是扣不');
        $model = new TradeOrder;
        return admin_transaction(function () use (&$model, $data) {

            $row = $model->query()
                ->where(['order_no' => $data->out_trade_no])
                //->where(['trade_status' => 0])
                ->first();
            if ($row) {
                if ($row->trade_status == 1) {
                    throw new ErrorException($message, 0, $level, $file, $line);
                }
                $row->trade_no = $data->trade_no;
                $row->trade_amount = $data->total_amount;
                $row->trade_status = 1; //支付成功
                $row->trade_time = $data->timestamp;
                if ($row->save()) {
                    //admin_order_feedback($row);
                }
            }
            return false;
        });
    }


}
