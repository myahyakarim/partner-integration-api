<?php

namespace App\Http\Controllers\Api;

use App\Domain\Partner\DTO\OutboundOrderCommand;
use App\Domain\Partner\Services\OutboundOrderService;
use App\Http\Controllers\Controller;
use App\Models\OutboundOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OutboundOrderController extends Controller
{
    public function store(Request $request, OutboundOrderService $service): JsonResponse
    {
        $data = $request->validate([
            'order_id' => ['required', 'string', 'max:64'],
            'partner_code' => ['required', 'string', 'max:32'],
            'fulfill_at' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sku' => ['required', 'string'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        $order = $service->accept(new OutboundOrderCommand(
            orderId: $data['order_id'],
            partnerCode: $data['partner_code'],
            items: $data['items'],
            fulfillAt: $data['fulfill_at'],
        ));

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'idempotency_key' => $order->idempotency_key,
        ], 202);
    }

    public function show(string $orderId): JsonResponse
    {
        $order = OutboundOrder::query()->where('order_id', $orderId)->firstOrFail();

        return response()->json($order);
    }
}
