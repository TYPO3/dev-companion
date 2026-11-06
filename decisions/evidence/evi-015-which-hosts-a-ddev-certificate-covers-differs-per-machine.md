---
id: D-EVI-015
title: Which hosts a DDEV certificate covers differs per machine
date: 2026-11-06
status: open
coveredBy:
  - HintsTest::aHandoverUrlIsReadWithItsCertificateValidated
---

# D-EVI-015 — Which hosts a DDEV certificate covers differs per machine

**No statement here says a host below the project has no certificate. The
session reads the URL it hands over with the certificate validated, and that
check holds on either kind of machine.**

## Evidence

- `feedback/archive/2026-10-31-143045-a-second-hostname-below-the-project-has-no.md`:
  one router served a wildcard for `*.ddev.site` alone, so
  `library.<project>.ddev.site` failed validation.
- Measured on 2026-11-06 on DDEV v1.25.5 with mkcert, on a throwaway project
  with `additional_hostnames` and `additional_fqdns`. The router served a
  certificate naming all three hosts, and `curl` without `-k` answered 200 on
  each.

## Decided

- **The check rather than the claim.** The two machines disagree, and nothing
  here established what separates them.

## Assumed

- That a session runs the check it reads. `SITE-13` holds a criterion on the
  URL.

## Wrong if

- A `SITE-13` run validates the URL and the user's browser still refuses it.
