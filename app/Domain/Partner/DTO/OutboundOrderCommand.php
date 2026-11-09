<?php

namespace App\Domain\Partner\DTO;

final readonly class OutboundOrderCommand
{
    public function __construct(
        public string $orderId,
        public string $partnerCode,
        public array $items,
        public string $fulfillAt,
    ) {}

    public function idempotencyKey(): string
    {
        return sprintf('harborpos:push:%s', $this->orderId);
    }
}
