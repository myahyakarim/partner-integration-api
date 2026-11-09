<?php

namespace Tests\Feature;

use App\Domain\Partner\Services\FakeHarborPosClient;
use App\Jobs\PushOrderToPartnerJob;
use App\Models\OutboundOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OutboundOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_accept_is_idempotent_and_dispatches_after_commit_job(): void
    {
        Queue::fake();

        $payload = [
            'order_id' => 'ord_9001',
            'partner_code' => 'harborpos',
            'fulfill_at' => '2026-11-10T18:00:00Z',
            'items' => [['sku' => 'BURGER', 'qty' => 2]],
        ];

        $this->postJson('/api/outbound-orders', $payload)->assertAccepted();
        $this->postJson('/api/outbound-orders', $payload)->assertAccepted();

        $this->assertSame(1, OutboundOrder::query()->count());
        Queue::assertPushed(PushOrderToPartnerJob::class, 1);
    }

    public function test_job_pushes_once_to_partner(): void
    {
        $order = OutboundOrder::query()->create([
            'order_id' => 'ord_9002',
            'partner_code' => 'harborpos',
            'idempotency_key' => 'harborpos:push:ord_9002',
            'items' => [['sku' => 'FRIES', 'qty' => 1]],
            'fulfill_at' => '2026-11-10T19:00:00Z',
            'status' => OutboundOrder::STATUS_PENDING,
        ]);

        $job = new PushOrderToPartnerJob($order->id);
        $job->handle($this->app->make(FakeHarborPosClient::class));
        $job->handle($this->app->make(FakeHarborPosClient::class));

        $order->refresh();
        $this->assertSame(OutboundOrder::STATUS_PUSHED, $order->status);
        $this->assertNotNull($order->partner_ticket_id);
        $this->assertSame(1, $this->app->make(FakeHarborPosClient::class)->callCount());
    }
}
