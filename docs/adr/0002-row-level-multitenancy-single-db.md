# ADR 0002: Row-level multitenancy on a single database

- Status: Accepted
- Date: 2026-06-14

## Context

The product must serve multiple schools (tenants) from one deployment. Options were
database-per-tenant, schema-per-tenant, or row-level isolation on a shared schema.
The expected tenant count and operational simplicity favor a single database.

## Decision

Use single-database, row-level multitenancy: every domain table carries a
`tenant_id` foreign key, and a global Eloquent scope filters all queries by the
current tenant. Tenant resolution happens in stages: first by the authenticated
user, later by subdomain.

## Consequences

- One schema, one migration set, one connection pool — simplest to operate.
- Isolation depends on the global scope being applied everywhere; isolation tests
  are mandatory (see Phase 4).
- The scope must be a no-op when no tenant is resolved (console, seeders, tests).
- Cross-tenant queries require explicitly bypassing the scope.
