<?php

namespace DagaSmart\Trade\Models;

use DagaSmart\BizAdmin\Models\BaseModel;
use Illuminate\Support\Facades\File;

/**
 * 图表模型
 */
class Chart extends BaseModel
{

    public function config()
    {

    }

    /**
     * 调用主题
     * 主题1：chalk、customized、dark、essos、essos-bold、infographic、infographic-bold、macarons
     * 主题2：purple-passion、roma、shine、vintage、walden、westeros
     * 主题3：wonderland
     * @param string|null $name
     * @return mixed
     */
    public function theme(?string $name = null): mixed
    {
        $name = $name ?? 'essos';
        try {
            if (File::exists(admin_chart_path("theme/{$name}.json"))) {
                $options = File::get(admin_chart_path("theme/{$name}.json"));
                return is_json($options) ? json_decode($options, true) : [];
            }
            return [];
        }catch (\Exception) {
            admin_abort("没有找到主题文件：chart/theme/{$name}.json");
        }
    }

}
