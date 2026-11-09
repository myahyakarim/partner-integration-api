<?php

namespace App\Jobs;

use App\Domain\Partner\Contracts\PartnerClient;
use App\Domain\Partner\DTO\OutboundOrderCommand;
use App\Models\OutboundOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PushOrderToPartnerJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public function __construct(public int $outboundOrderId) {}

    public function backoff(): array
    {
        return [1, 5, 15, 30];
    }

    public function handle(PartnerClient $partner): void
    {
        $order = OutboundOrder::query()->findOrFail($this->outboundOrderId);

        if ($order->status === OutboundOrder::STATUS_PUSHED) {
            return;
        }

        $command = new OutboundOrderCommand(
            orderId: $order->order_id,
            partnerCode: $order->partner_code,
            items: $order->items,
            fulfillAt: $order->fulfill_at,
        );

        try {
            $result = $partner->pushOrder($command);
            $order->update([
                'status' => OutboundOrder::STATUS_PUSHED,
                'partner_ticket_id' => $result['partner_ticket_id'],
                'last_error' => null,
            ]);
        } catch (\Throwable $e) {
            $order->update([
                'status' => OutboundOrder::STATUS_FAILED,
                'last_error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
