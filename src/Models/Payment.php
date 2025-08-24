<?php

namespace DagaSmart\Trade\Models;

use DagaSmart\BizAdmin\Models\BaseModel;
use Yansongda\Pay\Pay;


/**
 * 支付模型
 */
class Payment extends BaseModel
{
    const ALIAPY = '支付宝';
    const WECHAT = '微信支付';
    const DOUYIN = '抖音支付';
    const UNIPAY = '银联支付';

    protected array $source = ['soft' => '软件', 'recharge' => '充值', 'shop' => '商城'];

    public function channelOption(): array
    {
        return [
            'alipay' => static::ALIAPY,
            'wechat' => static::WECHAT,
            'douyin' => static::DOUYIN,
            'unipay' => static::UNIPAY,
        ];
    }

    public function channelAs($key): string|null
    {
       $option = $this->channelOption();
        return $option[$key] ?? null;
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
