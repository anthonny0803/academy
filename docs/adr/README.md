# Architecture Decision Records

This directory records the significant architectural decisions for the Academy
refactor (school management system → multi-tenant, modular, API-first SaaS).

Each ADR is immutable once accepted: if a decision changes, a new ADR supersedes
the old one (the old one is marked `Superseded by ADR-XXXX`) rather than edited.

Format: Status · Context · Decision · Consequences.

## Index

| ADR | Title | Status |
| --- | --- | --- |
| [0001](0001-api-first-and-blade-coexist.md) | API-first and Blade coexist | Accepted |
| [0002](0002-row-level-multitenancy-single-db.md) | Row-level multitenancy on a single database | Accepted |
| [0003](0003-pragmatic-repository-layer.md) | Pragmatic repository layer | Accepted |
| [0004](0004-ddd-lite-modular-domains.md) | DDD-lite modular domains | Accepted |
| [0005](0005-uuid-v4-and-postgresql.md) | UUID v4 primary keys and PostgreSQL | Accepted |
| [0006](0006-phased-execution-workflow.md) | Phased execution workflow | Accepted |
| [0007](0007-docker-multi-service-stack.md) | Docker multi-service stack | Accepted |
| [0008](0008-global-roles-and-one-to-one-membership.md) | Global roles and one-to-one tenant membership | Accepted |
| [0009](0009-separate-schema-from-behavior.md) | Separate schema from behavior | Accepted |
| [0010](0010-no-production-data.md) | No production data to preserve | Accepted |
