<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Payment;


/**
 * 支付服务类
 */
class PaymentService extends AdminService
{
    protected string $modelName = Payment::class;


}
