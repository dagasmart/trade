<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Payment;


/**
 * 支付异步回调类
 */
class NotifyService extends AdminService
{
    protected string $modelName = Payment::class;


}
