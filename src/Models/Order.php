<?php

namespace DagaSmart\Trade\Models;

use DagaSmart\BizAdmin\Models\BaseModel;


/**
 * 订单模型
 */
class Order extends BaseModel
{

    protected $table = 'trade_order';

    protected $casts = [
        'payer' => 'array',
        'trade_amount' => 'float',
        'refund_amount' => 'float',
    ];

    protected $appends = ['trade_status_as'];

    protected $fillable = ['order_id','order_no','base_order_no','order_source','trade_type','trade_channel','trade_no','trade_amount','is_plat','module','mer_id','payer_id','payer'];

    public function getTradeStatusAsAttribute(): ?string
    {
        $model = new Payment;
        $data = $model->statusOption();
        return $data[$this->trade_status] ?? null;
    }

    /**
     * 订单回写
     * @return void
     */
    public function feedback($data)
    {
        if ($data) {
            if (is_object($data)) {
                $data = get_object_vars($data);
            }
        }
    }

    /**
     * 订单流水记录
     */
    public function log($id): array
    {
        $data = [];
        $model = new Record;
        $rows = $model->query()->where(['id' => $id])->get();
        if ($rows) {
            foreach ($rows as $k => $row) {
                $data[$k]['time'] = Payment::typeOption($row->trade_type);
                $data[$k]['title'] = (string) $row->created_at;
                $data[$k]['detail'] = $row->trade_amount;
                $data[$k]['align'] = 'bottom';
                $data[$k]['color'] = getRandomColor();
                $data[$k]['backgroundColor'] = '#ccc';
            }
        }
        return $data;
    }


}

