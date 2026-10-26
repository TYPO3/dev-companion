---
id: D-EVI-012
title: A running environment says where it opens and the login
date: 2026-10-26
status: open
coveredBy:
  - EnvironmentsTest::aMadeInstallationSaysWhereItOpensAndHowToLogIn
---

# D-EVI-012 — A running environment says where it opens and the login

**`bin/cli environment:status` prints, for every running `E-SITE`, the frontend
URL, the backend path, the admin user and its password.**

## Evidence

- [`2026-10-26-121521-a-case-run`](../../feedback/archive/2026-10-26-121521-a-case-run-leaves-the-person-without-the.md).
  The maintainer judged a run of `SITE-10` and could open neither the frontend
  nor the backend. The password stood in `Environments::ADMIN_PASSWORD` alone.
- The run had gone into a copy outside this checkout, whose site base still
  named the original host. The frontend answered "No site configuration found".

## Decided

- **The password is printed.** `Environments::ADMIN_PASSWORD` already says why
  it is written down: it guards a throwaway site that only this machine reaches.
- **A copy keeps its base, and the status note says so** with the repair: the
  copy's DDEV host as the base, then a cache flush. `D-ANS-187` makes
  `typo3_project_describe` say it to a session in the copy.
- **Rejected: `environment:create` with a target outside the checkout.** No
  second run has needed it, and the note covers the one copy that was made.

## Wrong if

- A second run outside the checkout is set up by hand again, and the copy step
  costs more than the note.
