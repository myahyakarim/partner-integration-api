<?php

namespace App\Domain\Partner\Services;

use App\Domain\Partner\Contracts\PartnerClient;
use App\Domain\Partner\DTO\OutboundOrderCommand;
use Illuminate\Support\Str;

final class FakeHarborPosClient implements PartnerClient
{
    private int $calls = 0;

    public function pushOrder(OutboundOrderCommand $command): array
    {
        $this->calls++;

        return [
            'partner_ticket_id' => 'HPOS-'.Str::upper(Str::random(8)),
            'status' => 'accepted',
        ];
    }

    public function callCount(): int
    {
        return $this->calls;
    }
}
