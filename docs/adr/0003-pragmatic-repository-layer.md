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

## Amendment (2026-06-19)

`AcademicPeriod`, `Subject` and `Teacher` are added as aggregate roots with a
repository. They were missed in the original set despite owning their own controller,
listing and lifecycle, leaving inline queries in controllers/services (Fase 2 debt #4).
Each gets an interface plus an Eloquent implementation bound in
`RepositoryServiceProvider`; their call sites now route through the repository.

Clarifications fixed while applying this:

- **Query scopes stay in the models.** A scope (`search`, `active`, `inactive`,
  `orderByUserName`, ...) is reusable query vocabulary consumed by the repository and by
  relation closures (`withCount`, `whereHas`). The rule is not "the scope lives in the
  repository" but "only the repository — or a relation closure — invokes the scope".
- **The repository belongs to the aggregate root, not to every entity.** Sub-entities
  (`SectionSubjectTeacher`, `GradeColumn`) and the `SubjectTeacher` pivot do not get a
  repository.
- Cross-aggregate queries from `SectionSubjectTeacher` into `Enrollment`/`Grade` remain
  out of scope; they require a dedicated service injecting both repositories rather than
  a repository inside the model.
