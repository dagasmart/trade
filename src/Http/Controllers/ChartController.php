<?php

namespace DagaSmart\Trade\Http\Controllers;

use DagaSmart\BizAdmin\Controllers\AdminController;
use DagaSmart\Trade\Services\ChartService;
use Illuminate\Http\JsonResponse;


class ChartController extends AdminController
{
    protected string $serviceName = ChartService::class;

    public function test(): JsonResponse
    {
        $res = '{
                 "notMerge": true,
                 "notMerge": false,
                "title": {
                    "text": "销售情况"
                },
                "tooltip": {},
                "legend": {
                    "data": [
                        "销量",
                        "金额"
                    ]
                },
                "xAxis": {
                    "data": [
                        "衬衫",
                        "羊毛衫",
                        "雪纺衫",
                        "裤子",
                        "高跟鞋",
                        "袜子"
                    ]
                },
                "yAxis": {},
                "series": [
                    {
                        "name": "销量",
                        "type": "bar",
                        "data": [
                            17,
                            94,
                            87,
                            29,
                            11,
                            12
                        ]
                    },
                    {
                        "name": "金额",
                        "type": "bar",
                        "data": [
                            117,
                            294,
                            487,
                            129,
                            411,
                            112
                        ]
                    }
                ]
            }
        ';
        return $this->response()->success(json_decode($res, true));
    }
}
