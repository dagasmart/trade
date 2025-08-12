<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Record;


/**
 * 交易流水服务类
 */
class RecordService extends AdminService
{
    protected string $modelName = Record::class;

    /**
     * @return array
     */
    public function modeOption(): array
    {
        return $this->getModel()->modeOption();
    }

}
