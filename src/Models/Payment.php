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

    const NOPAY = '待付款';
    const PAYED = '已付款';
    const REFUNDED = '已退款';
    const PARTREFUND = '部分退款';

    protected array $source = ['soft' => '软件', 'recharge' => '充值', 'shop' => '商城'];

    public function sourceOption(): array
    {
        return $this->source;
    }

    public function statusOption(): array
    {
        return [
            '0' => static::NOPAY,
            '1' => static::PAYED,
            '-1' => static::REFUNDED,
            '-2' => static::PARTREFUND,
        ];
    }

    public function channelAs($key): string|null
    {
       $option = $this->channelOption();
        return $option[$key] ?? null;
    }

    public function channelOption(): array
    {
        return [
            'alipay' => static::ALIAPY,
            'wechat' => static::WECHAT,
            'douyin' => static::DOUYIN,
            'unipay' => static::UNIPAY,
        ];
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

}
