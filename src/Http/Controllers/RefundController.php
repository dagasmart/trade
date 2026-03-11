<?php

namespace DagaSmart\Trade\Http\Controllers;

use App\Library\Aes;
use DagaSmart\BizAdmin\Controllers\AdminController;
use DagaSmart\Trade\Services\RefundService;
use ErrorException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class RefundController extends AdminController
{

    protected string $serviceName = RefundService::class;


    /**
     * 交易退款
     * @param Request $request
     * @return JsonResponse
     */
    public function order(Request $request): JsonResponse
    {
        $id = $request->id ?? null;
        admin_abort_if(!$id, '交易id不能为空');

        $amount = $request->use_amount ?? null; //可退金额
        admin_abort_if(!$amount, '可退款金额不能为空');
        admin_abort_if(!is_numeric($amount), '可退款金额必须数字');
        admin_abort_if($amount <= 0, '可退款金额必须大于0');

        if ($this->service->refundOrder($id, $amount)) {
            return $this->response()->successMessage('退款操作成功');
        }
        return $this->response()->fail('退款操作失败');
    }



}
