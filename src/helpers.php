<?php

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

