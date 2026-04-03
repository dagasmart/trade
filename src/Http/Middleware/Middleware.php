<?php
declare(strict_types=1);
namespace DagaSmart\Trade\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Middleware
{

    public function handle(Request $request, Closure $next)
    {
        if (admin_extension_expiry('dagasmart.trade')) {
            return admin_response()->fail('软件已过期,请续费');
        }
        if (!admin_extension_enabled('dagasmart.trade')) {
            return admin_response()->fail('软件已禁用，请开启');
        }
        if (!class_exists(\Yansongda\Pay\Pay::class)) {
            return admin_response()->fail('缺少依赖包：composer require yansongda/pay');
        }
        return $next($request);
    }


}
