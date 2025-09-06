<?php

namespace DagaSmart\Trade\Http\Controllers;

use DagaSmart\BizAdmin\Controllers\AdminController;
use DagaSmart\BizAdmin\Renderers\Form;
use DagaSmart\BizAdmin\Renderers\Page;
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
        $crud = $this->baseCRUD()
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
            ->autoFillHeight(true)
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
                amis()->RadiosControl('trade_status', '交易状态')
                    ->options($this->service->statusOption()),
                amis()->Divider()->title('操作信息'),
                amis()->GroupControl()->mode('horizontal')->body([
                    amis()->TextControl('opera.user_id', '操作人ID'),
                    amis()->TextControl('opera.user_name', '操作人'),
                ]),
            ]),
        ])->static();
    }


}
