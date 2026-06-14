# ADR 0001: API-first and Blade coexist

- Status: Accepted
- Date: 2026-06-14

## Context

The application is a server-rendered Blade panel with a solid, single-responsibility
Service layer and only one public JSON endpoint. The SaaS goal requires a consumable
API (web/mobile clients, integrations, AI features) without discarding the working
Blade admin panel.

## Decision

Keep both delivery mechanisms over a shared core. The Service layer is the single
source of business logic. Web controllers render Blade; API controllers under
`/api/v1` return Resources following the project API contract (camelCase fields,
`{data}` / `{data, meta}` envelopes, error envelope, ISO 8601 dates, explicit nulls).

## Consequences

- No business logic in controllers; both entry points reuse Services.
- Two thin HTTP layers to maintain, but zero duplication of domain rules.
- The API contract is enforced at the Resource boundary, isolating internal naming
  (snake_case, PascalCase) from the public JSON layer.
