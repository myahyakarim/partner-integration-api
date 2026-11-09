# Partner Integration API

PHP / Laravel service that accepts outbound orders for partner **HarborPOS**, persists them locally, then pushes **after the DB transaction commits**.

Your API is only half the system. The partner is the other half.

## Problem

Firing a partner side effect inside an open transaction races the worker against uncommitted rows.
Replays of the same accept must not enqueue duplicate pushes.

## Approach

- Persist an `outbound_orders` record first
- Dispatch `PushOrderToPartnerJob` with `afterCommit()`
- Idempotent accept via stable key `harborpos:push:{order_id}`
- Explicit ownership: Northline owns the order; HarborPOS owns ticket state after accept

Details: [ARCHITECTURE.md](./ARCHITECTURE.md)

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan test
```

## Stack

PHP 8.3+ · Laravel · SQLite · PHPUnit
