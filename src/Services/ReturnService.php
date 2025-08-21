<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Payment;


/**
 * 支付同步回调类
 */
class ReturnService extends AdminService
{
    protected string $modelName = Payment::class;


}
