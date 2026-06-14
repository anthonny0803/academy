# ADR 0005: UUID v4 primary keys and PostgreSQL

- Status: Accepted
- Date: 2026-06-14

## Context

The system runs on SQLite (dev) with auto-incrementing `bigint` primary keys.
Sequential integer IDs leak record counts and are easy to enumerate across tenants,
and a real SaaS needs a production-grade engine with concurrency and rich types.

## Decision

Adopt PostgreSQL as the database engine and UUID v4 for all primary keys. Primary
keys become `uuid`, foreign keys use `foreignUuid`, and models use the `HasUuids`
trait (`keyType = string`, `incrementing = false`). Sanctum and spatie/permission
morph columns switch to UUID variants.

## Consequences

- Non-enumerable, globally unique IDs — safer for multi-tenant and public APIs.
- Method signatures and validation rules typed as `int` must change to `string`.
- Migrations, factories and seeders must generate UUIDs.
- Slightly larger keys/indexes than `bigint`; acceptable for the security and
  distribution benefits.
