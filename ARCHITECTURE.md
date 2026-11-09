# Architecture — Partner Integration API

Senior demo of treating an **external partner** as a product boundary, not “just HTTP.”

## Context

Fictional commerce platform **Northline** pushes fulfilled orders to partner **HarborPOS**.
HarborPOS owns kitchen/ticket state after accept.

## Critical rule

**Do not fire partner side effects before the local DB transaction commits.**

```
begin txn
  insert outbound_order (pending)
commit
  dispatch PushOrderToPartnerJob   ← afterCommit
```

If you dispatch inside the transaction and the worker runs before commit, the partner (or your reader) sees a ghost / missing row.

## Ownership

| Data | Owner |
|------|--------|
| Customer order, payment status | Northline |
| Ticket / prep status after accept | HarborPOS |
| Mapping `order_id ↔ partner_ticket_id` | Northline (integration table) |

## Failure design

- Outbound push is idempotent via `outbound_orders.idempotency_key`
- Partner client may be slow → job retries with backoff
- Inbound status webhooks update local projection only; they do not recreate orders

## Tradeoffs

| Choice | Why |
|--------|-----|
| afterCommit dispatch | Prevents race with uncommitted rows |
| Explicit ownership table | Avoids “who is source of truth?” ambiguity |
| Fake HarborPOS client | Portfolio-safe, testable |
