# ADR 0009: Separate schema from behavior

- Status: Accepted
- Date: 2026-06-14

## Context

Multitenancy needs both a schema change (`tenant_id` columns) and runtime behavior
(global scope, resolution middleware, current-tenant singleton). Introducing both at
once would force rewriting migrations and risk shipping enforcement before the data
model and backfill are ready.

## Decision

Add `tenant_id` as a nullable column on all domain tables in Phase 1b (schema only,
no enforcement). Activate the global scope, `ResolveTenant` middleware and tenant
resolution in Phase 4, then a final migration makes `tenant_id` NOT NULL after
backfill.

## Consequences

- Migrations are written once, not twice.
- Phases 1b–3 run with the column present but dormant; nothing enforces isolation yet.
- Enforcement is a deliberate, testable switch in Phase 4 (with isolation tests)
  rather than an implicit side effect of the schema.
