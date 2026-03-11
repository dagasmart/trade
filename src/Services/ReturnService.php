<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Payment;
use DagaSmart\Trade\Models\Order;
use DagaSmart\Trade\Models\Record;
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
        //throw new ErrorException('我是扣不');
        $model = new Order;
        return admin_transaction(function () use (&$model, $data) {

            $row = $model->query()
                ->where(['order_no' => $data->out_trade_no])
                //->where(['trade_status' => 0])
                ->first();
            if ($row) {
                if ($row->trade_status == 1) {
                    throw new ErrorException('已支付过，无须再付款', 0);
                }
                $row->trade_no = $data->trade_no;
                $row->trade_amount = $data->total_amount;
                $row->trade_status = 1; //支付成功
                $row->trade_time = $data->timestamp;
                if ($row->save()) {

                    //记录流水
                    $log = [
                        'trade_id' => $row->id,
                        'order_no' => $row->order_no,
                        'trade_type' => in_array($row->trade_status, [0,1]) ? 1 : 2,
                        'trade_channel' => $row->trade_channel,
                        'trade_amount' => $row->trade_amount,
                        'trade_status' => $row->trade_status,
                        'trade_code' => $row->trade_code,
                        'trade_result' => $row->trade_result,
                        'opera_type' => in_array($row->trade_status, [0,1]) ? 'user' : (admin_mer_id() ? 'mer' : 'plat'),
                        'opera_id' => $row->payer_id,
                        'opera' => $row->payer,
                    ];
                    $model = new Record;
                    $model->query()->insert($log);


                    //admin_order_feedback($row);
                }
            }
            return false;
        });
    }


}
