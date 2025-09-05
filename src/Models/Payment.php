<?php

namespace DagaSmart\Trade\Models;

use DagaSmart\BizAdmin\Models\BaseModel;


/**
 * 支付模型
 */
class Payment extends BaseModel
{
    //交易渠道
    const CHANNEL = ['alipay' => '支付宝', 'wechat' => '微信支付', 'douyin' => '抖音支付', 'unipay' => '银联支付'];

    const SUCCESS = '200';
    const FAIL = '300';
    const ERROR = '400';

    //交易类别
    const TYPE = ['1' => '支付', '2' => '退款'];

    //交易状态
    const STATUS = ['0' => '待付款', '1' => '已付款', '-1' => '已退款', '-2' => '部分退款'];

    //操作类别
    const OPERA = ['user' => '用户', 'mer' => '商家', 'plat' => '平台'];

    const SOURCE = ['soft' => '软件', 'recharge' => '充值', 'shop' => '商城',
    ];

    public function typeOption($key = null): array|string|null
    {
        return $key ? (static::TYPE[$key] ?? null) : static::TYPE;
    }

    public function sourceOption(): array
    {
        return static::SOURCE;
    }

    public function statusOption($key = null): array|string|null
    {
        return !is_null($key) ? (static::STATUS[$key] ?? null) : static::STATUS;
    }

    public function channelAs($key): string|null
    {
       $option = $this->channelOption();
        return $option[$key] ?? null;
    }

    public function channelOption($key = null): array|string|null
    {
        return $key ? (static::CHANNEL[$key] ?? null) : static::CHANNEL;
    }

    public function operaOption($key = null): array|string|null
    {
        return $key ? (static::OPERA[$key] ?? null) : static::OPERA;
    }

    /**
     * 是否平台
     * @param $key
     * @return bool
     */
    public function isPlat($key): bool
    {
        return in_array($key, array_keys(static::SOURCE));
    }

    /**
     * 交易订单来源
     * @param null $key
     * @return array|string|null
     */
    public function source($key = null): array|string|null
    {
        return $key ? (static::SOURCE[$key] ?? null) : static::SOURCE;
    }

    /**
     * 颜色标记
     * @param null $key
     * @return array|string|null
     */
    public function colorOption($key = null): array|string|null
    {
        $data = [
            '-3' => '#000',
            '-2' => '#ff9326',
            '-1' => '#4096ff',
            '0' => '#cccccc',
            '1' => '#30bf13',
            '2' => '#000',
            '3' => '#000',
            '4' => '#000',
            '5' => '#000',
        ];
        return $key ? ($data[$key] ?? null) : $data;
    }

}
