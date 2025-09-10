<?php

namespace DagaSmart\Trade\Models;

use DagaSmart\BizAdmin\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * 交易流水模型
 */
class Record extends BaseModel
{

    protected $table = 'trade_record';

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

    //流水交易记录
    public function log(): HasMany
    {
        return $this->hasMany(Log::class, 'record_id', 'id');
    }


}
