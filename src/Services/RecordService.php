<?php

namespace DagaSmart\Trade\Services;

use Carbon\Carbon;
use DagaSmart\BizAdmin\Services\AdminService;
use DagaSmart\Trade\Models\Chart;
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
     * 关联查询
     * @param $query
     * @param string $scene
     * @return void
     */
    public function addRelations($query, string $scene = 'detail'): void
    {
        parent::addRelations($query);
        $query->with(['log' => function ($query) {
            $query->orderBy('created_at','desc');
        }]);
    }

    /**
     * 支付网关模式
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

    public function config()
    {
        $chart = new Chart;
        return $chart->config();
    }

    public function theme($name = null)
    {
        $chart = new Chart;
        return $chart->theme($name);
    }

    public function chartData(): array
    {

//        $startDate = Carbon::parse(date('Y-m-d', strtotime('- 30 days'))); // 起始日期
//        $endDate = Carbon::parse(date('Y-m-d')); // 结束日期
//        $days = [];
//        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
//            $days[] = $date->toDateString(); // 输出格式为 Y-m-d
//        }
//        dump($days);
//        die;

        $sql = "
            SELECT
            a.days
            ,sum(case when b.id is not null AND b.trade_status=0 then trade_amount else 0 end) as nopay
            ,sum(case when b.id is not null AND b.trade_status=1 then trade_amount else 0 end) as payed
            ,sum(case when b.id is not null AND b.trade_status in('-1','-2') then trade_amount else 0 end) as refund
            FROM (SELECT generate_series(
                CURRENT_DATE - 6,  -- 开始日期
                CURRENT_DATE,  -- 结束日期
                '1 day'::interval    -- 间隔
            )::DATE AS days) a
            LEFT JOIN trade_record b ON a.days = b.created_at::date
            GROUP BY a.days
            ORDER BY a.days
        ";
        $rows = admin_sql_async($sql);

        $days = [];
        $series = [];
        if ($rows) {
            $days = array_column($rows, 'days');
            $series[] = [
                'name' => '待付款',
                'type' => 'bar',
                'data' => array_column($rows, 'nopay'),
            ];
            $series[] = [
                'name' => '已付款',
                'type' => 'bar',
                'data' => array_column($rows, 'payed'),
                'itemStyle' => [
                    //'borderRadius' => [10, 10, 0, 0],
                    // 阴影设置
                    'shadowBlur' => 2,           // 阴影的模糊大小
                    'shadowColor' => '#0002', // 阴影颜色
                    'shadowOffsetX' => 2,         // 阴影水平方向上的偏移
                    'shadowOffsetY' => -2,         // 阴影垂直方向上的偏移
                ]
            ];
            $series[] = [
                'name' => '已退款',
                'type' => 'bar',
                'data' => array_column($rows, 'refund'),
            ];
        }
        $colors = ['#4a66c9','#F44336','#E91E63','#9C27B0','#673AB7','#3F51B5','#2196F3'];
        $res = [
            'title' => [
                'text' => '近7日实时交易流水'
            ],
            'tooltip' => [
                'trigger' => 'axis',
                'axisPointer' => [
                    'type' => 'cross',
                    'label' => [
                        'backgroundColor' => $colors[array_rand($colors)],
                    ]
                ]
            ],
            'legend' => [
                'data' => array_column($series, 'name')
            ],
            'xAxis' => [
                'data' => $days
            ],
            'yAxis' => [],
            'series' => $series,
            'itemStyle' => [
                'borderRadius' => [50, 5, 0, 0],
            ]
        ];
        return $res;
    }

    public function statusData(): array
    {
        $keywords = request()->keywords;
        $status = array_search($keywords['status'] ?? null, Payment::STATUS);
        $trade_status = in_array($status, ['-1','-2']) ? ['-1','-2'] : [$status];
        $query_time = $keywords['time'] ?? Payment::UNDATED;
        $perPage = request()->perPage ?? 15;
        $order_no = request()->order_no ?? null;
        $data = $this->getModel()->with('log')
            ->when($order_no, function ($query) use ($order_no) {
                return $query->where('order_no', 'like', "%$order_no%");
            })
            ->whereIn('trade_status', $trade_status)
            ->whereBetween('created_at', [$query_time . ' 00:00:00', $query_time . ' 23:59:59'])
            ->paginate($perPage);
        return ['rows' => $data->items(), 'count' => $data->total()];
    }

}
