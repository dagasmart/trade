<?php

namespace DagaSmart\Trade\Http\Controllers;

use DagaSmart\BizAdmin\Controllers\AdminController;
use DagaSmart\BizAdmin\Renderers\Form;
use DagaSmart\BizAdmin\Renderers\Page;
use DagaSmart\BizAdmin\Support\Cores\AdminPipeline;
use DagaSmart\Trade\Services\OrderService;


class OrderController extends AdminController
{
    protected string $serviceName = OrderService::class;

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
                amis()->SelectControl('order_source', '订单来源')
                    ->options($this->service->sourceOption())
                    ->clearable(),
                amis()->SelectControl('trade_channel', '支付渠道')
                    ->options($this->service->channelOption())
                    ->clearable(),
                amis()->SelectControl('trade_status', '交易状态')
                    ->options($this->service->statusOption())
                    ->clearable(),
                amis()->Html()->html('<br>'),
                amis()->TextControl('trade_no', '交易号')
                    ->size('md')
                    ->clearable()
                    ->placeholder('请输入交易号'),
                amis()->DateRangeControl('trade_time', '交易时间')
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
                amis()->TableColumn('order_source', '订单来源')
                    ->searchable(['name' => 'order_source', 'type' => 'select', 'options' => $this->service->sourceOption(), 'clearable' => true])
                    ->set('type', 'select')
                    ->set('options', $this->service->sourceOption())
                    ->set('static', true),
                amis()->TableColumn('trade_channel', '支付渠道')
                    ->searchable(['name' => 'trade_channel', 'type' => 'select', 'options' => $this->service->channelOption(), 'clearable' => true])
                    ->set('type', 'input-tag')
                    ->set('options', $this->service->channelOption())
                    ->set('static', true),
                amis()->TableColumn('trade_amount', '交易金额')
                    ->set('type', 'Tpl')
                    ->tpl('<span style="color:orange">支付 ${trade_amount}</span><br>退款 ${refund_amount||0}<br><span style="color:orangered">成交 ${(trade_amount - refund_amount) | round}</span>')
                    ->width(120),
                amis()->TableColumn('trade_status_as', '交易状态')
                    ->searchable(['name' => 'trade_status', 'type' => 'select', 'options' => $this->service->statusOption(), 'clearable' => true])
                    ->set('type', 'tag')
                    ->set('displayMode', 'rounded')
                    ->set('color', '${trade_status == 1 ? "#30bf13" : (trade_status == -1 ? "#4096ff" : (trade_status == -2 ? "#ff9326" : "#ccc"))}')
                    ->set('size', 'xs')
                    ->set('static', true),
                amis()->TableColumn('trade_time', '交易时间')
                    ->searchable(['name' => 'trade_time', 'type' => 'input-date-range', 'format' => 'YYYY-MM-DD'])
                    ->set('width', 150),
                $this->rowActions([
                    $this->rowRefundButton(true, '', '退款')->visibleOn('${trade_status == 1 || trade_status == -2}'),
                    $this->rowShowButton(true),
                    $this->rowRecordButton('drawer', '', '流水记录'),
                    //$this->rowEditButton(true),
                    //$this->rowDeleteButton(),
                ])->set('width', 200)->set('align', 'right')->set('fixed', 'right')
            ]);

        return $this->baseList($crud);
    }

    /**
     * @param bool $isEdit
     * @return Form
     */
    public function form(bool $isEdit = false): Form
    {
        return $this->baseForm()->mode('horizontal')->tabs([
            // 基本信息
            amis()->Tab()->title('订单信息')->body([
                amis()->TextControl('order_no', '订单号')->static(),
                amis()->GroupControl()->mode('horizontal')->body([
                    amis()->GroupControl()->direction('vertical')->body([

                        amis()->TextControl('base_order_no', '原始单号')->static(),
                        amis()->RadiosControl('order_source', '订单来源')
                            ->options($this->service->sourceOption())
                            ->static(),
                        amis()->DateControl('created_at', '创建时间')->static(),
                        amis()->DateControl('updated_at', '更新时间')->static(),
                    ]),

                    amis()->GroupControl()->direction('vertical')->body([
                        amis()->ImageControl('trade_image',false)
                            ->thumbRatio('4:3')
                            ->thumbMode('cover h-full rounded-md overflow-hidden')
                            ->className(['overflow-hidden'=>true, 'h-full'=>true])
                            ->imageClassName([
                                'w-80'=>true,
                                'h-60'=>true,
                                'overflow-hidden'=>true
                            ])
                            ->fixedSize()
                            ->fixedSizeClassName([
                                'w-80'=>true,
                                'h-60'=>true,
                                'overflow-hidden'=>true
                            ])
                            ->crop([
                                'aspectRatio' => '1.3',
                            ])
                            ->static(),
                    ]),
                ]),
            ]),
            // 支付信息
            amis()->Tab()->title('交易信息')->body([
                amis()->RadiosControl('trade_channel', '支付渠道')
                    ->options($this->service->channelOption())
                    ->disabled(),
                amis()->RadiosControl('trade_status', '交易状态')
                    ->options($this->service->statusOption())
                    ->disabled(),
                amis()->TextControl('trade_no', '交易号')->static(),
                amis()->TextControl('trade_time', '交易时间')->static(),
                amis()->Divider()->title('买家信息'),
                amis()->GroupControl()->mode('horizontal')->body([
                    amis()->TextControl('payer.user_id', '买家ID')->static(),
                    amis()->TextControl('payer.user_name', '买家')->static(),
                ]),
            ]),
        ]);
    }

    public function detail(): Form
    {
        return $this->baseDetail()->title(false)->mode('horizontal')->tabs([
            // 基本信息
            amis()->Tab()->title('订单信息')->body([
                amis()->TextControl('order_no', '订单号')->static(),
                amis()->GroupControl()->mode('horizontal')->body([
                    amis()->GroupControl()->direction('vertical')->body([

                        amis()->TextControl('base_order_no', '原始单号')->static(),
                        amis()->RadiosControl('order_source', '订单来源')
                            ->options($this->service->sourceOption())
                            ->static(),
                        amis()->DateControl('created_at', '创建时间')->static(),
                        amis()->DateControl('updated_at', '更新时间')->static(),
                    ]),

                    amis()->GroupControl()->direction('vertical')->body([
                        amis()->ImageControl('school_logo',false)
                            ->thumbRatio('4:3')
                            ->thumbMode('cover h-full rounded-md overflow-hidden')
                            ->className(['overflow-hidden'=>true, 'h-full'=>true])
                            ->imageClassName([
                                'w-80'=>true,
                                'h-60'=>true,
                                'overflow-hidden'=>true
                            ])
                            ->fixedSize()
                            ->fixedSizeClassName([
                                'w-80'=>true,
                                'h-60'=>true,
                                'overflow-hidden'=>true
                            ])
                            ->crop([
                                'aspectRatio' => '1.3',
                            ]),
                    ]),
                ]),
            ]),
            // 支付信息
            amis()->Tab()->title('交易信息')->body([
                amis()->RadiosControl('trade_channel', '支付渠道')
                    ->options($this->service->channelOption())
                    ->disabled(),
                amis()->RadiosControl('trade_status', '交易状态')
                    ->options($this->service->statusOption())
                    ->disabled(),
                amis()->TextControl('trade_no', '交易号')->disabled(),
                amis()->TextControl('trade_time', '交易时间')->disabled(),
                amis()->Divider()->title('买家信息'),
                amis()->GroupControl()->mode('horizontal')->body([
                    amis()->TextControl('payer.user_id', '买家ID'),
                    amis()->TextControl('payer.user_name', '买家'),
                ]),
            ]),
        ])->static();
    }

    /**
     * 退款按钮
     * @param bool|string $dialog
     * @param string $dialogSize
     * @param string $title
     * @return mixed
     */
    protected function rowRefundButton(bool|string $dialog = false, string $dialogSize = 'md', string $title = ''): mixed
    {
        $title  = $title ?: admin_trans('admin.edit');
        $action = amis()->LinkAction()->link($this->getEditPath());

        if ($dialog) {
            $form = $this
                ->refundForm(true)
                ->api('put:/trade/refund/order/${id}')
                ->redirect('');
            if ($dialog === 'drawer') {
                $action = amis()->DrawerAction()->drawer(
                    amis()->Drawer()->closeOnEsc()->closeOnOutside()->title($title)->body($form)->size($dialogSize)
                );
            } else {
                $action = amis()->DialogAction()->dialog(
                    amis()->Dialog()->title($title)->body($form)->size($dialogSize)
                )->actions([
                    [
                        'type' => 'buttion',
                        'label' => '触发确认',
                        'onEvent' => [
                            'click' => [
                                'actions' => [
                                    [
                                        'actionType' => 'confirm'
                                    ]
                                ]
                            ]

                        ]
                    ]

                ]);





            }
        }
        $action->label($title)->level('link')->visible(admin_user()->administrator());
        return AdminPipeline::handle(AdminPipeline::PIPE_EDIT_ACTION, $action);
    }

    /**
     * 退款
     * @param bool $isEdit
     * @return Form
     */
    private function refundForm(bool $isEdit = false): Form
    {
        return $this->baseForm()->body([
            amis()->Alert()
                ->showIcon()
                ->style([
                    'color' => 'var(--colors-brand-6)',
                    'borderStyle' => 'dashed',
                    'borderColor' => 'var(--colors-brand-6)',
                    'backgroundColor' => 'var(--Tree-item-onChekced-bg)',
                ])
                ->body('提示：<br>退款成功后资金将无法追回,为避免产生纠纷和损失,请谨慎操作'),
            amis()->HiddenControl('id', 'ID')->static(),
            amis()->TextControl('order_no', '订单号')->static(),
            amis()->TextControl('trade_no', '交易号')->static(),
            amis()->TextControl('trade_amount', '交易金额')->static(),
            amis()->TagControl('refund_amount', '已退金额')->static(),
            amis()->TextControl('use_amount', '可退金额')
                ->addOn('元')
                ->validations('isNumeric,maximum:${(trade_amount - refund_amount) | round},minimum:0.01')
                ->size('sm')
                ->value('${(trade_amount - refund_amount) | round}')
                ->required(),
        ]);
    }

    /**
     * 流水按钮
     * @param bool|string $dialog
     * @param string $dialogSize
     * @param string $title
     * @return mixed
     */
    protected function rowRecordButton(bool|string $dialog = false, string $dialogSize = 'md', string $title = ''): mixed
    {
        $title  = $title ?: admin_trans('admin.log');
        $action = amis()->LinkAction()->link($this->getEditPath());

        if ($dialog) {
            $form = $this
                ->recordForm(false)
                ->api('get:/trade/order/${id}/log')
                ->redirect('');
            if ($dialog === 'drawer') {
                $action = amis()->DrawerAction()->drawer(
                    amis()->Drawer()->closeOnEsc()->closeOnOutside()->title($title)->body($form)->actions(false)->size($dialogSize)
                );
            } else {
                $action = amis()->DialogAction()->dialog(
                    amis()->Dialog()->title($title)->body($form)->size($dialogSize)
                );
            }
        }
        $action->label($title)->level('link')->visible(admin_user()->administrator());
        return AdminPipeline::handle(AdminPipeline::PIPE_EDIT_ACTION, $action);
    }

    /**
     * 流水时间轴
     * @return Page
     */
    private function recordForm(): Page
    {
        return $this->basePage()->body([
            amis()->Timeline()
                ->mode('right')
                ->direction('vertical')
                ->source('get:/trade/order/${id}/log'),
        ]);
    }

    public function log($id)
    {
        return $this->service->log($id);

    }

}
