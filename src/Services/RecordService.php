<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Payment;
use DagaSmart\Trade\Models\Record;


/**
 * 交易流水服务类
 */
class RecordService extends AdminService
{
    protected string $modelName = Record::class;

    /**
     * 排序
     * @param $query
     * @return void
     */
    public function sortable($query): void
    {
        if (!request()->orderBy) {
            $query->orderBy($this->primaryKey(),'desc');
        }
        parent::sortable($query);
    }

    /**
     * @return array
     */
    public function modeOption(): array
    {
        return $this->getModel()->modeOption();
    }

    public function typeOption(): array
    {
        $model = new Payment;
        return $model->typeOption();
    }

    public function channelOption(): array
    {
        $model = new Payment;
        return $model->channelOption();
    }

    public function statusOption(): array
    {
        $model = new Payment;
        return $model->statusOption();
    }

}
