<?php

namespace DagaSmart\Trade;

use DagaSmart\BizAdmin\Renderers\TextControl;
use DagaSmart\BizAdmin\Extend\ServiceProvider;

class TradeServiceProvider extends ServiceProvider
{
    protected $menu = [
        [
            'parent' => NULL,
            'title' => '支付交易',
            'url' => '/trade',
            'url_type' => 1,
            'icon' => 'ant-design:money-collect-outlined',
        ],
        [
            'parent' => '支付交易',
            'title' => '交易流水',
            'url' => '/trade/index',
            'url_type' => 1,
            'icon' => 'iconoir:database-stats',
        ],
        [
            'parent' => '支付交易',
            'title' => '账单结算',
            'url' => '/trade/settle',
            'url_type' => 1,
            'icon' => 'icon-park-outline:payment-method',
        ],
        [
            'parent' => '支付交易',
            'title' => '交易分析',
            'url' => '/trade/stat',
            'url_type' => 1,
            'icon' => 'gridicons:stats-alt',
        ],
        [
            'parent' => '支付交易',
            'title' => '支付设置',
            'url' => '/trade/settings',
            'url_type' => 1,
            'icon' => 'tabler:settings-dollar',
        ]
    ];

	public function settingForm()
	{
	    return $this->baseSettingForm()->body([
            TextControl::make()->name('value')->label('Value')->required(true),
	    ]);
	}
}
