# Partner Integration API

> Senior backend demo: **your API is only half the system — the partner is the other half.**

PHP / Laravel service that accepts an outbound order for fictional partner **HarborPOS**, persists it, then pushes **after DB commit**.

Clean-room portfolio project. No employer code or brands.

## Senior signals

- Explicit data ownership (who owns ticket status?)
- `afterCommit()` job dispatch (no race with uncommitted rows)
- Idempotent accept API
- Fake partner client + feature tests

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan test
```

## Architecture

See [ARCHITECTURE.md](./ARCHITECTURE.md).

## Stack

PHP 8.3+ · Laravel 13 · SQLite · PHPUnit
