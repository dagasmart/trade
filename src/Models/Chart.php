<?php

namespace DagaSmart\Trade\Models;


/**
 * 图表模型
 */
class Chart extends Model
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
     * @return array|null
     */
    public function theme(?string $name = null): ?array
    {
        try {
            $name = $name ?? 'essos';
            $file = admin_chart_path("theme/{$name}.json");
            if (file_exists($file)) {
                $fs = fopen($file, "r");
                $options = fread($fs, filesize($file));
                fclose($fs);
                return is_json($options) ? json_decode($options, true) : [];
            }
            return [];
        } catch (\Throwable $e) {
            admin_abort("文件读取失败: " . $e->getMessage());
        }
    }


}
