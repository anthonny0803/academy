# ADR 0004: DDD-lite modular domains

- Status: Accepted
- Date: 2026-06-14

## Context

All code lives under Laravel's default flat structure (`app/Models`, `app/Services`,
etc.). As the system grows into a SaaS with several bounded areas (Identity, Tenancy,
Students, Academics, Enrollments, Grades, AI), the flat layout obscures boundaries.

## Decision

Organize domain code under `app/Domains/{Domain}` with subfolders Models, Services,
Repositories, Resources, Requests, Policies, Enums. HTTP controllers stay in
`app/Http`. PSR-4 already maps `App\` to `app/`, so no autoload change is needed.

## Consequences

- Clear bounded contexts; each domain is self-contained and navigable.
- HTTP remains a thin, cross-cutting delivery layer outside the domains.
- Migration is incremental (domain by domain), using Students as the reference
  pattern; tests must stay green after each move.
