<?php

use DagaSmart\Trade\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('trade', [Controllers\TradeController::class, 'index']);
