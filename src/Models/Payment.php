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

    const CODE = ['200' => '成功', '300' => '失败', '400' => '错误'];

    //交易类别
    const TYPE = ['1' => '支付', '2' => '退款', '3' => '结算'];

    //交易状态
    const STATUS = ['0' => '待付款', '1' => '已付款', '2' => '已结算', '-1' => '已退款', '-2' => '部分退款'];

    //操作类别
    const OPERA = ['user' => '用户', 'mer' => '商家', 'plat' => '平台'];

    //交易来源
    const SOURCE = ['soft' => '软件', 'recharge' => '充值', 'shop' => '商城',
    ];

    //类别
    public function typeOption($key = null): array|string|null
    {
        return $key ? (static::TYPE[$key] ?? null) : static::TYPE;
    }

    //来源
    public function sourceOption(): array
    {
        return static::SOURCE;
    }

    //状态
    public function statusOption($key = null): array|string|null
    {
        return !is_null($key) ? (static::STATUS[$key] ?? null) : static::STATUS;
    }

    //渠道别名
    public function channelAs($key): string|null
    {
       $option = $this->channelOption();
        return $option[$key] ?? null;
    }

    //渠道
    public function channelOption($key = null): array|string|null
    {
        return $key ? (static::CHANNEL[$key] ?? null) : static::CHANNEL;
    }

    //操作类别
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
            '-3' => '#00008b',
            '-2' => '#ff9326',
            '-1' => '#ff0000',
            '0' => '#bbbbbb',
            '1' => '#30bf13',
            '2' => '#4096ff',
            '3' => '#EB4F4C',
            '4' => '#CF8E63',
            '5' => '#ECA1C3',
        ];
        return !is_null($key) ? ($data[$key] ?? null) : $data;
    }

}
