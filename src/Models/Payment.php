<?php

namespace DagaSmart\Trade\Models;

use DagaSmart\BizAdmin\Models\BaseModel;


/**
 * 支付模型
 */
class Payment extends BaseModel
{
    const ALIAPY = ['alipay' => '支付宝'];
    const WECHAT = ['wechat' => '微信支付'];
    const DOUYIN = ['douyin' => '抖音支付'];
    const UNIPAY = ['unipay' => '银联支付'];

    const SUCCESS = '200';
    const FAIL = '300';
    const ERROR = '400';

    const PAY = ['1' => '支付'];
    const REFUND = ['2' => '退款'];

    const NOPAY = ['0' => '待付款'];
    const PAYED = ['1' => '已付款'];
    const REFUNDED = ['-1' => '已退款'];
    const PARTREFUND = ['-2' => '部分退款'];

    const OPERA_USER = ['user' => '用户'];
    const OPERA_MER = ['mer' => '商家'];
    const OPERA_PLAT = ['plat' => '平台'];

    const SOURCE = ['soft' => '软件', 'recharge' => '充值', 'shop' => '商城'];

    public function typeOption($key = null): array|string|null
    {
        $data = [
            static::PAY,
            static::REFUND
        ];
        return $key ? ($data[$key] ?? null) : $data;
    }

    public function sourceOption(): array
    {
        return static::SOURCE;
    }

    public function statusOption($key = null): array|string|null
    {
        $data = [
            static::NOPAY,
            static::PAYED,
            static::REFUNDED,
            static::PARTREFUND,
        ];
        return !is_null($key) ? ($data[$key] ?? null) : $data;
    }

    public function channelAs($key): string|null
    {
       $option = $this->channelOption();
        return $option[$key] ?? null;
    }

    public function channelOption($key = null): array|string|null
    {
        $data = [
            static::ALIAPY,
            static::WECHAT,
            static::DOUYIN,
            static::UNIPAY,
        ];
        return $key ? ($data[$key] ?? null) : $data;
    }

    public function operaOption($key = null): array|string|null
    {
        $data = [
            static::OPERA_USER,
            static::OPERA_MER,
            static::OPERA_PLAT,
        ];
        return $key ? ($data[$key] ?? null) : $data;
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
