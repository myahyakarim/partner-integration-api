<?php

use App\Http\Controllers\Api\OutboundOrderController;
use Illuminate\Support\Facades\Route;

Route::post('/outbound-orders', [OutboundOrderController::class, 'store']);
Route::get('/outbound-orders/{orderId}', [OutboundOrderController::class, 'show']);
