<?php

namespace DagaSmart\Trade\Models;

use DagaSmart\BizAdmin\Models\BaseModel;


/**
 * 支付模型
 */
class Payment extends BaseModel
{
    const ALIAPY = '支付宝';
    const WECHAT = '微信支付';
    const DOUYIN = '抖音支付';
    const UNIPAY = '银联支付';

    const SUCCESS = '200';
    const FAIL = '300';
    const ERROR = '400';

    const PAY = '支付';
    const REFUND = '退款';

    const NOPAY = '待付款';
    const PAYED = '已付款';
    const REFUNDED = '已退款';
    const PARTREFUND = '部分退款';

    const OPERA_USER = '用户';
    const OPERA_MER = '商户';
    const OPERA_PLAT = '平台';

    protected array $source = ['soft' => '软件', 'recharge' => '充值', 'shop' => '商城'];

    public function typeOption($key = null): array|string|null
    {
        $data = [
            '1' => static::PAY,
            '2' => static::REFUND
        ];
        return $key ? ($data[$key] ?? null) : $data;
    }

    public function sourceOption(): array
    {
        return $this->source;
    }

    public function statusOption($key = null): array|string|null
    {
        $data = [
            '0' => static::NOPAY,
            '1' => static::PAYED,
            '-1' => static::REFUNDED,
            '-2' => static::PARTREFUND,
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
            'alipay' => static::ALIAPY,
            'wechat' => static::WECHAT,
            'douyin' => static::DOUYIN,
            'unipay' => static::UNIPAY,
        ];
        return $key ? ($data[$key] ?? null) : $data;
    }

    public function operaOption($key = null): array|string|null
    {
        $data = [
            'user' => static::OPERA_USER,
            'mer' => static::OPERA_MER,
            'plat' => static::OPERA_PLAT,
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
        return in_array($key, array_keys($this->source));
    }

    /**
     * 交易订单来源
     * @param null $key
     * @return array|string|null
     */
    public function source($key = null): array|string|null
    {
        return $key ? ($this->source[$key] ?? null) : $this->source;
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
