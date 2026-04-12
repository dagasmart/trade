<?php

namespace DagaSmart\Trade\Models;

use Yansongda\Pay\Pay;


/**
 * 交易模型
 */
class Trade extends Model
{

    //正常模式
    const int MODE_NORMAL = Pay::MODE_NORMAL;
    //沙箱模式
    const int MODE_SANDBOX = Pay::MODE_SANDBOX;
    //服务商模式
    const int MODE_SERVICE = Pay::MODE_SERVICE;


    public function modeOption(): array
    {
        return [
            ['value' => static::MODE_NORMAL, 'label' => '正常模式'],
            ['value' => static::MODE_SANDBOX, 'label' => '沙箱模式'],
            ['value' => static::MODE_SERVICE, 'label' => '服务商模式'],
        ];
    }



}
