<?php

namespace DagaSmart\Trade\Http\Controllers;

use DagaSmart\BizAdmin\Controllers\AdminController;
use DagaSmart\BizAdmin\Renderers\Form;
use DagaSmart\BizAdmin\Renderers\Page;
use DagaSmart\Trade\Services\OrderService;


class OrderController extends AdminController
{
    protected string $serviceName = OrderService::class;

    public function list(): Page
    {
        $crud = $this->baseCRUD()
            ->filterTogglable()
            ->headerToolbar([
                $this->createButton(true)->permission('biz.school.create'),
                ...$this->baseHeaderToolBar()
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
                    ->set('type', 'select')
                    ->set('options', $this->service->channelOption())
                    ->set('static', true),
                amis()->TableColumn('trade_amount', '交易金额'),
                amis()->TableColumn('trade_status_as', '交易状态')
                    ->searchable(['name' => 'trade_status', 'type' => 'select', 'options' => $this->service->statusOption(), 'clearable' => true])
                    ->set('type', 'tag')
                    ->set('displayMode', 'rounded')
                    ->set('color', '${trade_status == 1 ? "#30bf13" : (trade_status == -1 ? "#4096ff" : (trade_status == -2 ? "#ff9326" : "#ccc"))}')
                    ->set('size', 'xs')
                    ->set('static', true),
                amis()->TableColumn('trade_time', '交易时间')
                    ->searchable(['name' => 'trade_time', 'type' => 'input-date-range'])
                    ->set('width', 150),
                $this->rowActions([
                        $this->rowShowButton(true),
                        $this->rowEditButton(true),
                        $this->rowDeleteButton(),
                    ])
                    ->set('width', 200)
                    ->set('align', 'center')
                    ->set('fixed', 'right')
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
                amis()->Divider(),
                amis()->TextControl('trade_no', '交易号')->disabled(),
                amis()->TextControl('trade_time', '交易时间')->disabled(),
                amis()->GroupControl()->mode('horizontal')->body([
                    amis()->TextControl('contacts_mobile', '联系电话'),
                    amis()->TextControl('contacts_email', '联系邮件'),
                ]),
            ]),
        ]);
    }

    public function detail(): Form
    {
        return $this->baseDetail()->mode('horizontal')->tabs([
            // 基本信息
            amis()->Tab()->title('基本信息')->body([
                amis()->GroupControl()->mode('horizontal')->body([
                    amis()->GroupControl()->direction('vertical')->body([
                        amis()->TextControl('id', 'ID'),
                        amis()->TextControl('order_no', '订单号'),
                        amis()->SelectControl('school_nature', '学校性质')
                            ->options(),
                        amis()->SelectControl('school_type', '办学类型')
                            ->options(),
                        amis()->TextControl('register_time', '注册日期'),
                    ]),

                    amis()->GroupControl()->direction('vertical')->body([
                        amis()->Image()
                            ->thumbClassName(['overflow-hidden'=>true, 'w-80'=>true, 'h-60'=>true])
                            ->src('${school_logo}')
                            ->thumbMode('cover')
                            ->enlargeAble(),
                    ]),
                ]),
                amis()->Divider(),
                amis()->GroupControl()->direction('horizontal')->body([
                    amis()->TextControl('credit_code', '信用代码'),
                    amis()->TextControl('legal_person', '学校法人'),
                ]),
                amis()->Divider(),
                amis()->GroupControl()->mode('horizontal')->body([
                    amis()->TextControl('contacts_mobile', '联系电话'),
                    amis()->TextControl('contacts_email', '联系邮件'),
                ]),
                amis()->Divider(),
                amis()->InputCityControl('region', '所在地区')
                    ->searchable()
                    ->extractValue(false)
                    ->required(),
                amis()->TextControl('school_address', '学校地址'),
                amis()->TextControl('school_address_info', '详细地址')
                    ->value('${region.province} ${region.city} ${region.district} ${school_address}')
                    ->static(),
            ]),
        ])->static();
    }

}
