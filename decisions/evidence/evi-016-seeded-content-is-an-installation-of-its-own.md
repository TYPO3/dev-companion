---
id: D-EVI-016
title: Seeded content is an installation of its own
date: 2026-11-06
status: open
coveredBy:
  - EnvironmentsTest::aSeededInstallationHasANameAndADirectoryOfItsOwn
  - EnvironmentsTest::theCaminoContentIsRequiredBeforeTheSetupAndMovedToTheRoot
---

# D-EVI-016 — Seeded content is an installation of its own

**`bin/cli environment:create E-SITE <version> --camino` makes a second
installation with the content `theme_camino` ships. The default stays empty.**

## Evidence

- Built on 2026-11-06 on 14.3: 7 pages, 50 content elements, `base: /`, and the
  frontend read after the build.
- `.checkouts/14.3` and `main` ship the package, and `12.4` and `13.4` do not.
- `SITE-10` and `SITE-11` need an installation with nothing in it, and `SITE-12`
  needs one that renders.

## Decided

- **A flag rather than a default.** An environment nothing asked to seed stays
  the one the greenfield cases need.
- **A name of its own.** `D-EVI-006` holds one installation per line and
  database. The content is a third thing an installation differs by, so both can
  stand.
- `D-EVI-004` holds: what the package ships states the installation and judges
  nothing about it.

## Assumed

- That the import keeps writing `config/sites/camino/config.yaml`. Nothing else
  here names that identifier.

## Wrong if

- A release changes the identifier and the build stops at the base step.
