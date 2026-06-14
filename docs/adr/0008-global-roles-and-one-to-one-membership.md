# ADR 0008: Global roles and one-to-one tenant membership

- Status: Accepted
- Date: 2026-06-14

## Context

With spatie/laravel-permission under multitenancy, roles can be scoped per tenant via
the `teams` feature (`team_foreign_key = tenant_id`) or kept global (`teams = false`).
Teams add a composite key on `model_has_roles` and require setting a team context on
every request, seeder, test and job.

## Decision

Use global roles (`teams = false`) with one-to-one tenant membership: a user belongs
to exactly one tenant. The role catalog (admin, teacher, representative, student) is
product-defined and identical across tenants. Tenant isolation comes from `tenant_id`
plus the global scope (ADR 0002), not from the roles tables.

## Consequences

- Simplest permission setup; no team-context plumbing and no silent permission leaks
  from a forgotten context.
- `model_has_roles` keeps a UUID morph `model_id` without `team_foreign_key`.
- Reopen this decision only if N-to-many membership is adopted (a single user holding
  different roles across tenants); migrating to teams later is feasible.
