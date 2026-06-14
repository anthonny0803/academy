# ADR 0007: Docker multi-service stack

- Status: Accepted
- Date: 2026-06-14

## Context

Development ran on PHP's built-in server with SQLite and database-backed cache,
session and queues. The target stack uses PostgreSQL and Redis, plus background
workers and a scheduler, which must be reproducible across machines and CI.

## Decision

Provide a Docker Compose dev stack with services: `app` (php-fpm), `nginx`,
`postgres`, `redis`, `mailpit`, `queue` (worker) and `scheduler`. Redis backs cache,
session and queues. The development image (`docker/php/Dockerfile.dev`, php-fpm +
nginx) is separate from the existing production image (`dockerfile`, Apache, used by
Render). Healthchecks gate startup via `/up`, `pg_isready` and `redis-cli ping`.

## Consequences

- One command (`docker compose up`) reproduces the full stack locally.
- Dev/prod parity on engines (PostgreSQL, Redis) without coupling the two images.
- The dev image installs the `redis` PHP extension via PECL (the project uses
  `phpredis`, not `predis`).
- File ownership is handled with `UID`/`GID` build args to avoid root-owned files on
  Linux bind mounts.
