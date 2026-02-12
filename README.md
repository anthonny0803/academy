# Academy — School Management System

> **[Versión en español más abajo](#academy--sistema-de-gestión-escolar)**

A comprehensive school management system built with Laravel, designed to handle the full academic lifecycle: from student enrollment to weighted grade calculations and academic period closures. Built as a portfolio project demonstrating backend architecture, business logic modeling, and professional development practices.

### [Academy — Live System →](https://academy.ybanez.dev)

> **Test credentials:** admin@gmail.com / admin123

Grades Portal (React): [portal-academy.ybanez.dev](https://portal-academy.ybanez.dev) | [Source Code](https://github.com/anthonny0803/portal-academy)

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12+, PHP 8.2+ |
| Database | MySQL (dev) / PostgreSQL (prod) |
| Frontend | Blade + Tailwind CSS + Flowbite |
| Auth | Laravel Breeze + Spatie Permission |
| Public API | REST API + React SPA (Vite) |
| Deployment | Render + Neon PostgreSQL |

---

## Architecture

The project follows a **vertical slice architecture** where each entity is a self-contained module with clear separation of responsibilities:

```
Controller  →  Orchestration & authorization
Policy      →  Authorization rules (helper methods + policy methods)
Request     →  Input validation & sanitization
Service     →  Business logic (DB transactions, state management)
Model       →  Relationships, scopes, mutators, domain helpers
```

Every service uses `DB::transaction()` when modifying multiple records, ensuring data consistency — no orphaned records.

### Key Architectural Decisions

**Single Responsibility across layers** — Controllers never contain business logic. Policies never check data validity. Requests never modify state. Services own all business rules.

**Dual activation model** — `users.is_active` controls system access, while profile tables (`teachers.is_active`, `students.is_active`, `representatives.is_active`) control domain-level status. A teacher with `user.is_active = false` but `teacher.is_active = true` can still access the system — because the middleware checks both.

**Cascading state management** — When an academic period closes, the system completes enrollments → calculates pass/fail → deactivates sections → deactivates students without active enrollments → syncs representative status. All within a single transaction.

---

## Role Hierarchy

```
Developer (boolean field, not a Spatie role)
    └── Supervisor
        └── Admin
            └── Teacher
                └── Representative (no system access)
                    └── Student (no system access)
```

A user can hold multiple roles simultaneously (e.g., a Teacher who is also a Representative). The only restriction: Supervisor and Admin are mutually exclusive.

Representatives and Students never access the web application — their data is managed by administrative staff. They can only consult grades through the public API portal.

---

## Core Business Logic

### Weighted Grade Calculation

Each subject assignment (Section-Subject-Teacher) has grade columns with weights that must sum to exactly 100%. The system calculates weighted averages per subject and determines pass/fail status per enrollment:

```
Subject: Mathematics
├── Midterm 1    (weight: 30%)  →  grade: 85  →  weighted: 25.50
├── Midterm 2    (weight: 30%)  →  grade: 70  →  weighted: 21.00
└── Final Exam   (weight: 40%)  →  grade: 60  →  weighted: 24.00
                                    ─────────────────────────
                                    Weighted Average: 70.50 (PASS, min: 60)
```

### Enrollment State Machine

```
Active → Completed (period closure — automatic)
Active → Promoted (moved to another section within same period)
Active → Transferred (student leaves the institution)
Active → Withdrawn (student drops out)
```

Each transition triggers cascading updates: student situation changes, representative status syncs, and data integrity validations.

### Academic Period Closure

The most complex operation in the system. When a period closes:

1. Validates all grade configurations are complete (weights = 100%)
2. Verifies all enrolled students have all grades loaded
3. Calculates pass/fail for every active enrollment
4. Completes all enrollments with their results
5. Deactivates all sections in the period
6. Deactivates students without remaining active enrollments
7. Syncs representative activation status
8. Logs every operation for audit trail

---

## Public Grades API

A REST API secured with Bearer token authentication, consumed by a React SPA ([portal-academy](https://github.com/anthonny0803/portal-academy)) where students and representatives can consult grades using their document ID and birth date.

```
GET /api/public/student/grades?document_id=V12345678&birth_date=2000-05-15
GET /api/public/representative/grades?document_id=V12345678&birth_date=1975-03-20
```

The API returns a nested structure: student → enrollments → subjects → grade columns → individual grades with weighted averages.

---

## Project Structure

```
app/
├── Contracts/          # Interfaces (HasEntityName)
├── Enums/              # EnrollmentStatus, Role, Sex, StudentSituation, RelationshipType
├── Http/
│   ├── Controllers/    # 14 controllers (including Api/)
│   ├── Middleware/      # CheckActiveUser, PreventBackHistory, ValidatePublicApiToken
│   └── Requests/       # Organized by entity (Store + Update + specific actions)
├── Models/             # 12 models with relationships, scopes, mutators
├── Policies/           # 12 policies with helper method pattern
├── Services/           # ~30 services organized by entity
└── Traits/             # Activatable, AuthorizesRedirect, CanToggleActivation
```

---

## Local Setup

> The easiest way to explore Academy is through the [live system](https://academy.ybanez.dev). If you want to run it locally:

```bash
git clone https://github.com/anthonny0803/academy.git
cd academy
composer install && npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev & php artisan serve
```

---

## Author

**Anthonny Ybanez** — Backend Developer
- [LinkedIn](https://linkedin.com/in/anthonny0803)
- [GitHub](https://github.com/anthonny0803)
- [Web](https://ybanez.dev)

---
---

# Academy — Sistema de Gestión Escolar

Un sistema integral de gestión escolar construido con Laravel, diseñado para manejar el ciclo académico completo: desde la inscripción de estudiantes hasta el cálculo de calificaciones ponderadas y el cierre de períodos académicos. Construido como proyecto de portafolio demostrando arquitectura backend, modelado de lógica de negocio y prácticas de desarrollo profesional.

### [Academy — Sistema en Vivo →](https://academy.ybanez.dev)

> **Credenciales de prueba:** admin@gmail.com / admin123

Portal de Calificaciones (React): [portal-academy.ybanez.dev](https://portal-academy.ybanez.dev) | [Codigo Fuente](https://github.com/anthonny0803/portal-academy)

---

## Stack Tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 12+, PHP 8.2+ |
| Base de datos | MySQL (desarrollo) / PostgreSQL (producción) |
| Frontend | Blade + Tailwind CSS + Flowbite |
| Autenticación | Laravel Breeze + Spatie Permission |
| API Pública | REST API + React SPA (Vite) |
| Despliegue | Render + Neon PostgreSQL |

---

## Arquitectura

El proyecto sigue una **arquitectura de corte vertical** donde cada entidad es un módulo autocontenido con clara separación de responsabilidades:

```
Controller  →  Orquestación y autorización
Policy      →  Reglas de autorización (helpers + methods)
Request     →  Validación y sanitización de datos
Service     →  Lógica de negocio (transacciones BD, gestión de estados)
Model       →  Relaciones, scopes, mutators, helpers de dominio
```

Cada servicio usa `DB::transaction()` cuando modifica múltiples registros, garantizando consistencia — sin registros huérfanos.

### Decisiones Arquitectónicas Clave

**Responsabilidad Única entre capas** — Los Controllers nunca contienen lógica de negocio. Las Policies nunca verifican validez de datos. Los Requests nunca modifican estado. Los Services son dueños de todas las reglas de negocio.

**Modelo de activación dual** — `users.is_active` controla el acceso al sistema, mientras las tablas de perfil (`teachers.is_active`, `students.is_active`, `representatives.is_active`) controlan el estado a nivel de dominio. Un profesor con `user.is_active = false` pero `teacher.is_active = true` puede acceder al sistema — porque el middleware verifica ambos.

**Gestión de estados en cascada** — Cuando un período académico se cierra, el sistema completa inscripciones → calcula aprobado/reprobado → desactiva secciones → desactiva estudiantes sin inscripciones activas → sincroniza estado de representantes. Todo dentro de una sola transacción.

---

## Jerarquía de Roles

```
Developer (campo booleano, no es un rol de Spatie)
    └── Supervisor
        └── Administrador
            └── Profesor
                └── Representante (sin acceso al sistema)
                    └── Estudiante (sin acceso al sistema)
```

Un usuario puede tener múltiples roles simultáneamente (ej: un Profesor que también es Representante). La única restricción: Supervisor y Admin son mutuamente excluyentes.

Los Representantes y Estudiantes nunca acceden a la aplicación web — sus datos son gestionados por el personal administrativo. Solo pueden consultar calificaciones a través del portal de API pública.

---

## Lógica de Negocio Principal

### Cálculo de Calificaciones Ponderadas

Cada asignación de materia (Sección-Materia-Profesor) tiene columnas de calificación con pesos que deben sumar exactamente 100%. El sistema calcula promedios ponderados por materia y determina el estado aprobado/reprobado por inscripción:

```
Materia: Matemáticas
├── Parcial 1      (peso: 30%)  →  nota: 85  →  ponderado: 25.50
├── Parcial 2      (peso: 30%)  →  nota: 70  →  ponderado: 21.00
└── Examen Final   (peso: 40%)  →  nota: 60  →  ponderado: 24.00
                                    ─────────────────────────
                                    Promedio Ponderado: 70.50 (APROBADO, mín: 60)
```

### Máquina de Estados de Inscripción

```
Activo → Completado (cierre de período — automático)
Activo → Promovido (movido a otra sección dentro del mismo período)
Activo → Transferido (el estudiante sale de la institución)
Activo → Retirado (el estudiante abandona)
```

Cada transición desencadena actualizaciones en cascada: cambios de situación del estudiante, sincronización de estado del representante y validaciones de integridad de datos.

### Cierre de Período Académico

La operación más compleja del sistema. Cuando un período se cierra:

1. Valida que todas las configuraciones de calificación estén completas (pesos = 100%)
2. Verifica que todos los estudiantes inscritos tengan todas las notas cargadas
3. Calcula aprobado/reprobado para cada inscripción activa
4. Completa todas las inscripciones con sus resultados
5. Desactiva todas las secciones del período
6. Desactiva estudiantes sin inscripciones activas restantes
7. Sincroniza estado de activación de representantes
8. Registra cada operación para auditoría

---

## API Pública de Calificaciones

Una API REST protegida con autenticación Bearer token, consumida por una SPA React ([portal-academy](https://github.com/anthonny0803/portal-academy)) donde estudiantes y representantes pueden consultar calificaciones usando su documento de identidad y fecha de nacimiento.

```
GET /api/public/student/grades?document_id=V12345678&birth_date=2000-05-15
GET /api/public/representative/grades?document_id=V12345678&birth_date=1975-03-20
```

La API retorna una estructura anidada: estudiante → inscripciones → materias → columnas de calificación → notas individuales con promedios ponderados.

---

## Estructura del Proyecto

```
app/
├── Contracts/          # Interfaces (HasEntityName)
├── Enums/              # EnrollmentStatus, Role, Sex, StudentSituation, RelationshipType
├── Http/
│   ├── Controllers/    # 14 controllers (incluyendo Api/)
│   ├── Middleware/      # CheckActiveUser, PreventBackHistory, ValidatePublicApiToken
│   └── Requests/       # Organizados por entidad (Store + Update + acciones específicas)
├── Models/             # 12 modelos con relaciones, scopes, mutators
├── Policies/           # 12 policies con patrón de helper methods
├── Services/           # ~30 servicios organizados por entidad
└── Traits/             # Activatable, AuthorizesRedirect, CanToggleActivation
```

---

## Instalación Local

> La forma más fácil de explorar Academy es a través del [sistema en vivo](https://academy.ybanez.dev). Si quieres ejecutarlo localmente:

```bash
git clone https://github.com/anthonny0803/academy.git
cd academy
composer install && npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run dev & php artisan serve
```

---

## Autor

**Anthonny Ybanez** — Desarrollador Backend
- [LinkedIn](https://linkedin.com/in/anthonny0803)
- [GitHub](https://github.com/anthonny0803)
- [Web](https://ybanez.dev)