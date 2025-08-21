<?php

namespace DagaSmart\Trade\Http\Controllers;

use DagaSmart\BizAdmin\Controllers\AdminController;
use DagaSmart\Trade\Services\ReturnService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ReturnController extends AdminController
{

    protected string $serviceName = ReturnService::class;

    public function __construct(ReturnService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function alipay(Request $request)
    {
        dump($request);
    }

}
