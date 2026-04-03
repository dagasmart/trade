<?php
declare(strict_types=1);
namespace DagaSmart\Trade\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Middleware
{

    public function handle(Request $request, Closure $next)
    {
        if (!admin_extension_enabled('dagasmart.abcc')) {
            return admin_response()->fail('请在已订购软件里启用');
        }
        if (!admin_extension_enabled('dagasmart.abcc')) {
            return admin_response()->fail('请在已订购软件里启用');
        }
        return $next($request);
    }


}
