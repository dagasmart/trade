<?php

namespace DagaSmart\Trade\Models;

use DagaSmart\BizAdmin\Models\BaseModel;


/**
 * 交易流水模型
 */
class Record extends BaseModel
{

    protected $table = 'trade_order_log';

    protected $primaryKey = 'id';

    protected $casts = [
        'trade_result' => 'array',
        'opera' => 'array',
    ];


}
