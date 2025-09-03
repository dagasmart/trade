<?php

namespace DagaSmart\Trade\Models;

use DagaSmart\BizAdmin\Models\BaseModel;


/**
 * 订单模型
 */
class Order extends BaseModel
{

    protected $table = 'trade_order';

    protected $primaryKey = 'id';

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
     * @param $id
     * @return array
     */
    public function log($id): array
    {
        $data = [];
        $order_amount = $this->query()->where(['id' => $id])->value('trade_amount');
        $model = new Record;
        $rows = $model->query()->where(['trade_id' => $id])->orderBy('id')->get();
        if ($rows) {
            $model = new Payment;
            foreach ($rows as $k => $row) {
                if (!in_array($row->trade_status, [0,1])) {
                    $refund_amount = $row->trade_amount;
                    $order_amount = bcsub($order_amount, $refund_amount,2);
                }
                $data[$k]['id'] = $row->id;
                $data[$k]['time'] = $model->statusOption($row->trade_status);
                $data[$k]['cardSchema'] = [
                    'type' => 'card',
                    'className' => ['p-2' => true, 'shadow' => true],
                    'header' => [
                        'title' => (string) $row->created_at,
                        'subTitle' => $model->typeOption($row->trade_type) . PHP_EOL . (float) $row->trade_amount . '元',
                        'description' => '操作' . PHP_EOL . ($row->opera['user_name'] ?? null),
                        'avatarText' => $model->operaOption($row->opera_type),
                    ],
                    'body' => [
                        amis()->Divider(),
                        amis()->Html()->html($model->typeOption($row->trade_type)
                            . ($row->trade_status == 0 ? '中' : '成功，成交金额' . PHP_EOL . '<b class="text-red-500">' . $order_amount . '</b>元')),
                    ],
                ];
                $data[$k]['align'] = 'top';
                $data[$k]['color'] = $model->colorOption($row->trade_status);
                $data[$k]['dotSize'] = $k == 0 ? 'lg' : 'md';
                $data[$k]['lineColor'] = $data[$k]['color'];
                $data[$k]['backgroundColor'] = '#bbb';
            }
        }
        $ids = array_column($data, 'id');
        array_multisort($data, SORT_DESC, $ids);
        return $data;
    }



}

