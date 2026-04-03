<?php

namespace DagaSmart\Trade\Http\Controllers;


class TradeController extends AdminController
{
    public function index()
    {
        $page = $this->basePage()->body('Trade Extension.');

        return $this->response()->success($page);
    }
}
