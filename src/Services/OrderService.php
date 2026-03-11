<?php

namespace DagaSmart\Trade\Services;

use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Payment;
use DagaSmart\Trade\Models\Order;


/**
 * 交易流水服务类
 */
class OrderService extends AdminService
{
    protected string $modelName = Order::class;

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

    public function log($id): array
    {
        return $this->getModel()->log($id);
    }

    public function statusOption(): array
    {
        $model = new Payment;
        return $model->statusOption();
    }

    public function sourceOption(): array
    {
        $model = new Payment;
        return $model->sourceOption();
    }

    public function channelOption(): array
    {
        $model = new Payment;
        return $model->channelOption();
    }

}
