<?php

namespace DagaSmart\Trade\Models;

use DagaSmart\BizAdmin\Models\BaseModel;


/**
 * 交易模型
 */
class TradeOrder extends BaseModel
{

    protected $table = 'trade_order';

    protected $fillable = ['order_id','order_no','base_order_no','order_source','trade_type','trade_channel','trade_amount','is_plat','module','mer_id','payer_id','payer'];




}

