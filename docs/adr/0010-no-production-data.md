# ADR 0010: No production data to preserve

- Status: Accepted
- Date: 2026-06-14

## Context

The refactor changes primary keys from `bigint` to UUID and rewrites migrations.
Whether this requires data migration scripts depends on the existence of live data.

## Decision

There is no production data to preserve. Migrations may be rewritten in place and the
schema reset freely (`migrate:fresh`) during the refactor. Demo data is provided
through seeders and factories, not through data-migration scripts.

## Consequences

- The `create_*_table` migrations can be edited directly to UUID instead of layering
  reversible alter migrations.
- No backfill/data-migration tooling is needed for the key change.
- This assumption must be revisited the moment the system holds real tenant data;
  after that, destructive migrations are no longer acceptable.
