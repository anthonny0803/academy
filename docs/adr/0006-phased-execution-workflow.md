# ADR 0006: Phased execution workflow

- Status: Accepted
- Date: 2026-06-14

## Context

The refactor is large and touches the schema, the domain layout, the API and the
multitenancy behavior. Doing it in one branch would be unreviewable and risky.

## Decision

Execute the refactor in numbered phases. Each phase is one issue, one branch from
`develop`, and one Pull Request. Phase 1 is split into 1a/1b/1c because of its size.
Every phase ships code plus factories, seeders and unit + feature tests, and runs
`/audit` and `/security-review` before opening the PR. The tracker lives in
`progress.md` (source of truth).

## Consequences

- Small, reviewable, reversible PRs; clear progress checkpoints.
- No phase closes without a green suite and up-to-date factories/seeders.
- Some ordering constraints (e.g. schema changes before behavior) are encoded into
  the phase sequence to avoid rewriting work twice.
