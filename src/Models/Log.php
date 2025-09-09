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

    protected $appends = ['trade_status_as', 'trade_color'];

    protected $casts = [
        'trade_result' => 'array',
        'opera' => 'array',
        'trade_amount' => 'float',
    ];

    public function getTradeStatusAsAttribute(): ?string
    {
        $model = new Payment;
        $data = $model->statusOption();
        return $data[$this->trade_status] ?? null;
    }

    public function getTradeColorAttribute(): array|string|null
    {
        $model = new Payment;
        return $model->colorOption($this->trade_status);
    }


}
