<?php

namespace App\Domain\Partner\Services;

use App\Domain\Partner\DTO\OutboundOrderCommand;
use App\Jobs\PushOrderToPartnerJob;
use App\Models\OutboundOrder;
use Illuminate\Support\Facades\DB;

final class OutboundOrderService
{
    /**
     * Persist locally, then dispatch partner push only after commit.
     */
    public function accept(OutboundOrderCommand $command): OutboundOrder
    {
        $order = DB::transaction(function () use ($command) {
            return OutboundOrder::query()->firstOrCreate(
                ['idempotency_key' => $command->idempotencyKey()],
                [
                    'order_id' => $command->orderId,
                    'partner_code' => $command->partnerCode,
                    'items' => $command->items,
                    'fulfill_at' => $command->fulfillAt,
                    'status' => OutboundOrder::STATUS_PENDING,
                ],
            );
        });

        // Only the first accept enqueues work; replays are no-ops.
        if ($order->wasRecentlyCreated) {
            PushOrderToPartnerJob::dispatch($order->id)->afterCommit();
        }

        return $order;
    }
}
