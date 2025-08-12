<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Trade;


/**
 * 账单结算服务类
 */
class SettleService extends AdminService
{
    protected string $modelName = Trade::class;

    /**
     * @return array
     */
    public function modeOption(): array
    {
        return $this->getModel()->modeOption();
    }

}
