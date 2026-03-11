<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Trade;


/**
 * 交易服务类
 */
class TradeService extends AdminService
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
