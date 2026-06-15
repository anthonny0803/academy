# ADR 0005: UUID v7 primary keys and PostgreSQL

- Status: Accepted
- Date: 2026-06-14
- Amended: 2026-06-15 (v4 → v7; see Decision)

## Context

The system ran on SQLite (dev) with auto-incrementing `bigint` primary keys.
Sequential integer IDs leak record counts and are easy to enumerate across tenants,
and a real SaaS needs a production-grade engine with concurrency and rich types.

## Decision

Adopt PostgreSQL as the database engine and UUID for all primary keys. Primary
keys become `uuid`, foreign keys use `foreignUuid`, and models use the `HasUuids`
trait (`keyType = string`, `incrementing = false`). Sanctum and spatie/permission
morph columns switch to UUID variants.

The UUID version is **v7** (time-ordered), which is what Laravel's `HasUuids` trait
generates by default. The original draft labelled this "v4"; that was shorthand for
"non-sequential, non-enumerable identifier" and conflicted with the mandated
`HasUuids` trait, which does not produce v4. The label is corrected to v7 to match
the implementation and keep the operational instruction (`HasUuids`) authoritative.

## Consequences

- Non-enumerable, globally unique IDs — safer for multi-tenant and public APIs.
- Time-ordered keys insert at the tail of the B-tree index instead of at random
  positions, reducing page splits and fragmentation — the main performance pitfall
  of random (v4) UUID primary keys in PostgreSQL.
- The `uuid` validation rule accepts v7 without a version argument, so Form Requests
  need no special handling.
- Method signatures and validation rules typed as `int` must change to `string`.
- Migrations, factories and seeders must generate UUIDs.
- Trade-off accepted: v7 embeds creation time in the ID. For internal school-domain
  records this is irrelevant — `created_at` already exists and the API exposes no
  ID-based ordering.
- Slightly larger keys/indexes than `bigint`; acceptable for the security and
  distribution benefits.
