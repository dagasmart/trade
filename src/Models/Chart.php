<?php

namespace DagaSmart\Trade\Models;

use DagaSmart\BizAdmin\Models\BaseModel;
use Ripple\File\Exception\FileException;


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
     * @param null $name
     * @return mixed
     * @throws FileException
     */
    public function theme($name = null): mixed
    {
        $name = $name ?? 'essos';
        $options = \Ripple\File\File::getContents(admin_chart_path("theme/{$name}.json"));
        return json_decode($options, true);
    }


}
