<?php

namespace DagaSmart\Trade\Http\Controllers;

use DagaSmart\BizAdmin\Controllers\AdminController;
use DagaSmart\BizAdmin\Renderers\Form;
use DagaSmart\BizAdmin\Renderers\Page;
use DagaSmart\BizAdmin\Renderers\Panel;
use DagaSmart\BizAdmin\Support\Cores\AdminPipeline;
use DagaSmart\Trade\Services\RecordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class RecordController extends AdminController
{
    protected string $serviceName = RecordService::class;

    public function list(): Page
    {
        $crud[] = $this->basePage()->body([
            $this->getChartData()->set('md', 12),
            amis()->Grid()->className('mb-1')->columns([
                $this->pieChart()->set('md', 3),
                $this->pieChart()->set('md', 3),
                $this->barChart()->set('md', 6),
            ]),
        ]);
        $crud[] = $this->baseCRUD()
            ->filterTogglable()
            ->headerToolbar([
                amis('reload')->align('right'),
                amis('filter-toggler')->align('right'),
            ])
            ->filter($this->baseFilter()->body([
                amis()->TextControl('order_no', '订单号')
                    ->size('md')
                    ->clearable()
                    ->placeholder('请输入订单号'),
                amis()->SelectControl('trade_type', '操作类型')
                    ->options($this->service->typeOption())
                    ->clearable(),
                amis()->SelectControl('trade_channel', '支付渠道')
                    ->options($this->service->channelOption())
                    ->clearable(),
                amis()->SelectControl('trade_status', '交易状态')
                    ->options($this->service->statusOption())
                    ->clearable(),
                amis()->Html()->html('<br>'),
                amis()->DateRangeControl('trade_time', '操作时间')
                    ->format('YYYY-MM-DD')
                    ->clearValueOnHidden()
            ]))
            ->autoFillHeight(false)
            ->columns([
                amis()->TableColumn('id', 'ID')->sortable()->set('fixed','left'),
                amis()->TableColumn('order_no', '订单号')
                    ->searchable(['placeholder' => '请输入订单号', 'clearable' => true])
                    ->copyable()
                    ->width(280)
                    ->set('fixed','left'),
                amis()->TableColumn('trade_type', '操作类型')
                    ->searchable(['name' => 'trade_type', 'type' => 'select', 'options' => $this->service->typeOption(), 'clearable' => true])
                    ->set('type', 'select')
                    ->set('options', $this->service->typeOption())
                    ->set('align','center')
                    ->set('static', true),
                amis()->TableColumn('trade_channel', '支付渠道')
                    ->searchable(['name' => 'trade_channel', 'type' => 'select', 'options' => $this->service->channelOption(), 'clearable' => true])
                    ->set('type', 'input-tag')
                    ->set('options', $this->service->channelOption())
                    ->set('align','center')
                    ->set('static', true),
                amis()->TableColumn('trade_amount', '交易金额')
                    ->set('type', 'Tpl')
                    ->tpl('<span style="color: ${trade_color}">${trade_type == 2 ? "-" : ""}${trade_amount}</span>')
                    ->width(120),
                amis()->TableColumn('trade_status_as', '交易状态')
                    ->searchable(['name' => 'trade_status', 'type' => 'select', 'options' => $this->service->statusOption(), 'clearable' => true])
                    ->set('type', 'tag')
                    ->set('displayMode', 'status')
                    ->set('color', '${trade_color}')
                    ->set('size', 'xs')
                    ->set('align','center')
                    ->set('static', true),
                amis()->TableColumn('created_at', '操作时间')
                    ->searchable(['name' => 'trade_time', 'type' => 'input-date-range', 'format' => 'YYYY-MM-DD'])
                    ->set('width', 150),
                $this->rowActions([
                    $this->rowShowButton(true),
                ])->set('width', 80)->set('align', 'center')->set('fixed', 'right')
            ]);

        return $this->baseList($crud);
    }

    public function detail(): Form
    {
        return $this->baseDetail()->title(false)->mode('horizontal')->tabs([
            // 订单信息
            amis()->Tab()->title('订单信息')->body([
                amis()->TextControl('order_no', '订单号'),
                amis()->GroupControl()->mode('horizontal')->body([
                    amis()->GroupControl()->direction('vertical')->body([
                        amis()->RadiosControl('trade_type', '操作类型')
                            ->options($this->service->typeOption()),
                        amis()->TagControl('trade_channel', '支付渠道')
                            ->options($this->service->channelOption()),
                        amis()->DateTimeControl('created_at', '创建时间'),
                        amis()->DateTimeControl('updated_at', '更新时间'),
                    ]),
                ]),
            ]),
            // 交易信息
            amis()->Tab()->title('交易信息')->body([
                amis()->TagControl('trade_status', '交易状态')
                    ->options($this->service->statusOption()),
                amis()->Divider()->title('操作信息'),
                amis()->GroupControl()->mode('horizontal')->body([
                    amis()->TextControl('opera.user_id', '操作人ID'),
                    amis()->TextControl('opera.user_name', '操作人'),
                ]),
                amis()->GroupControl()->mode('horizontal')->body([
                    amis()->TextControl('opera.mer_id', '交易商户'),
                    amis()->TextControl('opera.module', '交易模块'),
                ]),
            ]),
        ])->static();
    }

    public function getChartData(): Panel
    {
        return amis()->Panel()->className('w-full h-100')->body([
            amis()->Chart()
                ->api(admin_url('trade/record/chart/data'))
                ->interval(30000)
                ->chartTheme($this->service->theme())
                ->config([
                    'xAxis' => ['type' => 'category'],
                    'yAxis' => ['type' => 'value'],
                    'backgroundColor' => 'rgba(242,234,191,0)',
                    'grid' => ['left' => '5%', 'right' => '0%', 'top' => 60, 'bottom' => 30],
                ])
                ->onEvent([
                    'click' => [
                        'actions' => [
                            [
                                'actionType' => 'drawer',
                                'drawer' => [
                                    'title' => '${seriesName}【流水记录】',
                                    'draggable' => true,
                                    'actions' => [],
                                    'closeOnOutside' => true,
                                    'closeOnEsc' => true,
                                    'size' => 'lg',
                                    'body' => [
                                        amis()->Page()->body([
                                            amis()->CRUDTable()
                                                ->api(admin_url('trade/record/chart/status/data?page=${page}&date=${name}&name=${seriesName}'))
                                                ->affixHeader()
                                                ->filterTogglable()
                                                ->filterDefaultVisible()
                                                ->filter(
                                                    $this->baseFilter()->body([
                                                        amis()->TextControl('keywords', '订单号')
                                                            ->placeholder('请输入订单号')
                                                            ->size('md'),
                                                    ])
                                                )
                                                ->headerToolbar([
                                                   amis('reload')->align('right'),
                                                   amis('filter-toggler')->align('right'),
                                                ])
                                                ->autoFillHeight(true)
                                                ->columns([
                                                    amis()->TableColumn('order_no', '订单号')->align('center'),
                                                    amis()->TableColumn('created_at', '时间')->align('center'),
                                                    amis()->TableColumn('trade_amount', '金额')->align('center'),
                                                    $this->rowActions([
                                                        $this->rowShowButton(true),
                                                    ])->set('width', 80)->set('align', 'center')->set('fixed', 'right')->hiddenOn('${seriesName=="待付款"}')
                                                ]),
                                        ]),

                                    ],

                                ]
                            ]
                        ]
                    ]
                ]),
        ])
        ->id('pie-chart-panel')->set('animations', [
            'enter' => [
                'delay'    => 0.1,
                'duration' => 0.5,
                'type'     => 'zoomIn',
            ],
        ]);
    }










    public function pieChart(): Panel
    {
        return amis()->Panel()->className('w-full h-100')->body([
            amis()->Chart()->config([
                'backgroundColor' => '',
                'tooltip'         => ['trigger' => 'item'],
                'legend'          => ['bottom' => 0, 'left' => 'center'],
                'series'          => [
                    [
                        'name'              => '交易额',
                        'type'              => 'pie',
                        'radius'            => ['40%', '70%'],
                        'avoidLabelOverlap' => false,
                        'itemStyle'         => ['borderRadius' => 10, 'borderColor' => '#fff', 'borderWidth' => 2],
                        'label'             => ['show' => false, 'position' => 'center'],
                        'emphasis'          => [
                            'label' => [
                                'show'       => true,
                                'fontSize'   => '40',
                                'fontWeight' => 'bold',
                            ],
                        ],
                        'labelLine'         => ['show' => true],
                        'data'              => [
                            ['value' => 1048, 'name' => '待付款'],
                            ['value' => 735, 'name' => '已付款'],
                            ['value' => 580, 'name' => '已结算'],
                            ['value' => 484, 'name' => '已退款'],
                            ['value' => 300, 'name' => '部分退款'],
                        ],
                    ],
                ],
            ])
        ])->id('pie-chart-panel')->set('animations', [
            'enter' => [
                'delay'    => 0.1,
                'duration' => 0.5,
                'type'     => 'zoomIn',
            ],
        ]);
    }

    public function barChart($height = null): Panel
    {
        return amis()->Panel()->className('w-full h-100')->body([
            amis()->Chart()->height($height)->config([
                'backgroundColor' => '',
                'title'           => [
                    'text' => '近6个月交易情况',
                    'subtext' => '交易盘点'
                ],
                'tooltip'         => ['trigger' => 'axis'],
                'xAxis'           => [
                    'type'        => 'category',
                    'boundaryGap' => true,
                    'data'        => ['1月', '2月', '3月', '4月', '5月', '6月'],
                ],
                'yAxis'           => ['type' => 'value'],
                'grid'            => ['left' => '5%', 'right' => '0%', 'top' => 60, 'bottom' => 30,],
                'legend'          => ['data' => ['支付', '退款']],
                'series'          => [
                    [
                        'name' => '支付',
                        'type' => 'bar',
                        'smooth' => true,
                        'color' => '#30bf13',
                        'data' => [320, 132, 201, 334, 190, 130],
                        'areaStyle' => [],
                        'showBackground' => false,
                        'itemStyle' => [
                            'borderRadius' => [8, 8, 0, 0],
                            // 阴影设置
                            'shadowBlur' => 2,           // 阴影的模糊大小
                            'shadowColor' => '#0001', // 阴影颜色
                            'shadowOffsetX' => 2,         // 阴影水平方向上的偏移
                            'shadowOffsetY' => -2,         // 阴影垂直方向上的偏移
                        ]
                    ],
                    [
                        'name' => '退款',
                        'type' => 'bar',
                        'smooth' => false,
                        'color' => '#ff9326',//fad287
                        'data' => [120, 332, 401, 134, 90, 630],
                        'areaStyle' => [],
                        'showBackground' => false,
                        'itemStyle' => [
                            'borderRadius' => [8, 8, 0, 0],
                            // 阴影设置
                            'shadowBlur' => 2,           // 阴影的模糊大小
                            'shadowColor' => '#0001', // 阴影颜色
                            'shadowOffsetX' => 2,         // 阴影水平方向上的偏移
                            'shadowOffsetY' => -2,         // 阴影垂直方向上的偏移
                        ]
                    ],
                    [
                        'name' => '退款率',
                        'type' => 'line',
                        'smooth' => false,
                        'color' => '#00008b',
                        'data' => [20, 32, 41, 14, 9, 60],
                        'areaStyle' => null,
                        'showBackground' => false,
                        'timelineItemBorderWidth' => 1,
                        'symbolSize' => 6, // 设置圆圈的大小
                        'lineStyle' => [
                            'width' => 1,  // 设置线条宽度为1
                        ],
                        'itemStyle' => [
                            'borderRadius' => [8, 8, 0, 0],
                            // 阴影设置
                            'shadowBlur' => 1,           // 阴影的模糊大小
                            'shadowColor' => '#0003', // 阴影颜色
                            'shadowOffsetX' => 1,         // 阴影水平方向上的偏移
                            'shadowOffsetY' => -1,         // 阴影垂直方向上的偏移
                        ]
                    ],
                ],
            ])
        ])->id('pie-chart-panel')->set('animations', [
            'enter' => [
                'delay'    => 0.1,
                'duration' => 0.5,
                'type'     => 'zoomIn',
            ],
        ]);
    }

    public function chartData(): JsonResponse
    {
        $res = $this->service->chartData();
        return $this->response()->success($res);
    }

    public function statusData(): JsonResponse
    {
        $res = $this->service->statusData();
        return $this->response()->success($res);
    }


}
