---
id: D-ANS-189
title: A changelog sweep keeps any of several tags
date: 2026-10-26
status: open
coveredBy:
  - PackageSourcesTest::aSweepKeepsTheEntriesThatCarryAnyOfTheTagsGiven
---

# D-ANS-189 — A changelog sweep keeps any of several tags

**`typo3_changelog_lookup` takes `tags`, a list, and keeps the entries that
carry any of them. The sitepackage skill sweeps its major with the kinds of file
a sitepackage ships.**

## Evidence

- [`2026-10-26-160545-deprecation-sweep`](../../feedback/archive/2026-10-26-160545-deprecation-sweep-of-v14-returns-75-entries.md).
  Four runs of `SITE-10` read 75 deprecations of 14 and needed one to three. The
  same sweep with `TypoScript`, `TSConfig`, `Fluid`, `YAML` and `Frontend`
  returned 16 on 2026-10-26.
- `D-ANS-093` measured one call for a whole major against eleven tag calls,
  which is why `tag` alone was not the answer.

## Decided

- **A list beside `tag`, read as any of them.** `D-ANS-017` keeps every argument
  to one type.
- **The entries stay whole.** The feedback asked for title, issue and tags
  alone. A sweep reads the migration from the same entry, so the short form
  would cost a second call per finding.

## Wrong if

- A sitepackage run misses a deprecation that bit it, because its tags were none
  of the five.
