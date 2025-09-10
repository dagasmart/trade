<?php

namespace DagaSmart\Trade\Models;

use DagaSmart\BizAdmin\Models\BaseModel;


/**
 * 交易记录模型
 */
class Log extends BaseModel
{

    protected $table = 'trade_log';

    protected $primaryKey = 'id';

    protected $appends = ['trade_code_as', 'trade_code_color', 'trade_result_items'];

    protected $casts = [
        'trade_result' => 'array',
        'opera' => 'array',
        'trade_amount' => 'float',
        'trade_code' => 'int',
    ];

    public function getTradeCodeAsAttribute(): ?string
    {
        $model = new Payment;
        $data = $model->codeOption();
        return $data[$this->trade_code] ?? null;
    }

    public function getTradeCodeColorAttribute(): array|string|null
    {
        $model = new Payment;
        return $model->codeColorOption($this->trade_code);
    }

        public function getTradeResultItemsAttribute()
    {
        $data = [];
        $items = $this->trade_result;
        if ($items && is_array($items)) {
            unset($items['fund_bill_list']);
            foreach ($items as $name => $value) {
                if (admin_trade_trans($name)) {
                    $data[] = [
                        'label' => admin_trade_trans($name),
                        'content' => $value,
                        'span' => 4,
                    ];
                }
            }
        }
        return $data;

    }


}
