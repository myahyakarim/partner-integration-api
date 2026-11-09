<?php

namespace App\Domain\Partner\Contracts;

use App\Domain\Partner\DTO\OutboundOrderCommand;

interface PartnerClient
{
    /** @return array{partner_ticket_id: string, status: string} */
    public function pushOrder(OutboundOrderCommand $command): array;
}
