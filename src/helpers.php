<?php

use DagaSmart\Trade\TradeServiceProvider;

if (!function_exists('trade_pay_config')) {
    /**
     * 获取支付配置参数
     * @param array $data
     * @return array
     */
    function trade_pay_config(array $data = []): array
    {
        return settings()->pay('payment', $data);
    }
}



if (!function_exists('trade_order_sn')) {
    /**
     * 微秒时间戳转换日期序列生成交易订单号
     */
    function trade_order_sn($microtime = null, $channel = null): string
    {
        $microtime = $microtime ?? microtime(true);
        $channel = $channel ? strtoupper($channel) : null;
        return $channel . date('YmdHis' . str_replace('.', '', fmod($microtime,1)), $microtime);
    }
}

if (!function_exists('admin_trade_trans')) {
    /**
     * 语言包
     * @param $key
     * @return array|string|null
     */
    function admin_trade_trans($key): array|string|null
    {
        return TradeServiceProvider::trans('trade.' . $key) ?? null;
    }
}
