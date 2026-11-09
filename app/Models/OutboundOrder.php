<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundOrder extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PUSHED = 'pushed';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'order_id',
        'partner_code',
        'idempotency_key',
        'items',
        'fulfill_at',
        'status',
        'partner_ticket_id',
        'last_error',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
        ];
    }
}
