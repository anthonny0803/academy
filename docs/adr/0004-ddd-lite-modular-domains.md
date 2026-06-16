# ADR 0004: DDD-lite modular domains

- Status: Accepted
- Date: 2026-06-14
- Amended: 2026-06-16 (HTTP delivery moved inside each domain — see Amendment below)

## Context

All code lives under Laravel's default flat structure (`app/Models`, `app/Services`,
etc.). As the system grows into a SaaS with several bounded areas (Identity, Tenancy,
Students, Academics, Enrollments, Grades, AI), the flat layout obscures boundaries.

## Decision

Organize code by domain under `app/Domains/{Domain}` as full vertical slices. Each
domain owns both its core (`Models`, `Services`, `Policies`, `Enums`) and its delivery
layer under `Http/` (`Controllers`, `Requests`, and domain-specific `Middleware`).
Cross-cutting code lives in a `Shared` kernel module (`app/Domains/Shared`) holding
`Contracts`, `Traits`, `Enums`, `Services` and the shared `Http` pieces (the base
`Controller`, the `PreventBackHistory` middleware and shared form requests).

The Blade presentation layer (`resources/views` and the `app/View/Components` classes)
stays outside the domains as a separate, replaceable consumer of the application —
ready to coexist with a future SPA (React/Vue/Next) that consumes the API.

PSR-4 already maps `App\` to `app/`, so no autoload change is needed. Polymorphic types
are decoupled from class namespaces via a morph map, and factories are resolved by a
flat name resolver, both registered in `AppServiceProvider`.

## Consequences

- Each domain is a self-contained, navigable package ("package by feature"): the
  structure screams the business, not the framework.
- `app/Http` is removed entirely; HTTP delivery is no longer a separate global layer.
- Presentation is decoupled from the domain, easing an eventual API-first SPA client.
- Migration is incremental (domain by domain), using Students as the reference
  pattern; tests must stay green after each move.

## Amendment (2026-06-16)

The original decision kept HTTP controllers in `app/Http` as a thin cross-cutting
delivery layer outside the domains. This was reversed in favour of full vertical
slices: each domain now owns its `Http/` layer. Rationale:

- **Extreme order at scale**: everything a domain needs lives in one place, so the
  layout keeps making sense as the app grows.
- **API-first decoupling**: with a future React/Vue/Next client in mind, the HTTP
  delivery belongs to the domain it serves, while the Blade presentation is kept apart
  as just one consumer. Base `Controller` and non-domain middleware remain in `Shared`
  because they are genuinely cross-cutting, not business logic.
