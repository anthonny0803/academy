# ADR 0003: Pragmatic repository layer

- Status: Accepted
- Date: 2026-06-14

## Context

The codebase has no repository abstraction; queries live inline in controllers and
Services. A full repository-per-entity approach adds ceremony with little payoff for
trivial CRUD, but real query logic benefits from being isolated and testable.

## Decision

Introduce repositories only for aggregates with genuine query logic: Student,
Enrollment, Grade, User, Representative, Section. Each gets an interface plus an
Eloquent implementation, bound in `RepositoryServiceProvider` and injected into
Services. Trivial CRUD stays in Services without a repository.

## Consequences

- Services depend on interfaces, not Eloquent (Dependency Inversion); query logic is
  unit-testable in isolation.
- No hypothetical abstraction: repositories appear only where duplication or real
  querying justifies them.
- A consistent place to move inline queries (e.g. `StudentController` listings,
  join-based scopes).
