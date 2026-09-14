---
id: D-DIS-023
title: The container is assembled a second time to be read
date: 2026-09-01
status: open
coveredBy:
  - ServiceLookupTest::aContainerThatDoesNotAssembleIsTheAnswer
  - ServiceLookupTest::anAliasCarriesTheClassBehindIt
---

# D-DIS-023 — The container is assembled a second time to be read

**A compiled container has forgotten its private services. So what answers is
the builder the core's own `ContainerBuilder` produces, reached by reflection
and read after it compiles.**

Nearly every TYPO3 service is private, and a caller who asks which class stands
behind an interface asks about exactly those.

## Evidence

- Measured against `.environments/e-site-14.3` on 2026-09-01: the assembly
  yields 1212 definitions and 230 aliases in 1.9 seconds, boot included.
- `ContainerBuilder::buildContainer()` is `protected` and returns the builder
  after it compiles it. Its signature — `PackageManager`,
  `ServiceProviderRegistry` — and its position stand unchanged on 12.4, 13.4,
  14.3 and main.
- What it does is not repeatable from outside without a copy of it. Two compiler
  passes at fixed priorities, and the project's `config/system/services.*` only
  where the public path differs from the project path. Every active package's
  `Configuration/Services.*`, and the synthetic `_early.*` services with their
  aliases.
- The early instances come back out of the live container, which carries them
  under `_early.<id>`.
- After compilation the definitions carry their arguments resolved, so the
  answer for a constructor is the ids that really land in it. `PageRenderer`
  comes back with `IconRegistry_decorated_1` at position 10, which is the
  decoration no `Services.yaml` states.

## Decided

- Reflection into `buildContainer()` rather than a copy of it. A copy drifts
  when the core changes its assembly and nothing fails. A reflection call
  throws, and the probe reports the topic `unavailable` with the exception,
  which is the failure this repository prefers.
- Read after compilation rather than before. What survives is what the live
  container has, and a definition removed as unused is not a service a caller
  can ask for.
- Asked for rather than read with everything else. It costs a second assembly,
  and a caller who asked about an icon should not pay for it.
- Nothing is instantiated. The probe reads the builder and never calls `get()`,
  so no service constructor runs to answer a question about it.
- `typo3_service_lookup` is exempt from the fixture root of `D-DOC-012`. A
  fixture that could answer it would have to be a TYPO3 rather than a shape of
  one. This repository does not depend on `symfony/dependency-injection`, so a
  stub would put a fabricated service list on a documentation page.

## Assumed

- That four lines with the same signature mean the next one will have it too.
  What that costs when it no longer holds is one topic that answers
  `unavailable`. The tool reports that as the reason rather than as an empty
  list.
- That a second assembly is safe to run beside the first. It compiles into
  memory, dumps nothing into the cache the installation reads, and replaces no
  container.

## Wrong if

- The assembly diverges from the live container, through a compiler pass
  registered elsewhere, or a service provider that behaves differently on a
  second run. Then the answer describes a container nobody uses.
- The 1.9 seconds becomes minutes on an installation with many packages, which
  would make the answer one a caller stops to ask for.
- A caller reads `public: false` as a prohibition rather than as what it is. The
  container hands the service to whoever declares it, and only `makeInstance`
  needs it public.
