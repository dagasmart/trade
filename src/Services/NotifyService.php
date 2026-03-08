<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\Trade\Models\Payment;


/**
 * 支付异步回调类
 */
class NotifyService extends AdminService
{
    protected string $modelName = Payment::class;


}
