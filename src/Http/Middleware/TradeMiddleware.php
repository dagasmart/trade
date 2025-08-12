<?php

namespace DagaSmart\Trade\Http\Middleware;

use Closure;

class TradeMiddleware
{

    public function handle($request, Closure $next)
    {
        admin_abort(223423);die;

        return $next($request);
    }

}
