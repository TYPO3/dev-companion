---
id: D-DOC-052
title: A revoked entry names no test
date: 2026-08-23
status: open
restsOn: [D-DOC-048]
coveredBy:
  - DecisionsTest::aRevokedEntryNamesNoTest
---

# D-DOC-052 — A revoked entry names no test

**A test that declares a revoked decision fails `bin/cli decisions:check`, and
the attribute belongs on the entry that revoked it.**

A revoked statement no longer describes this server, so a test that declares it
claims to hold something the repository says it no longer does.

## Evidence

- Read on 2026-08-23. Of 39 revoked decisions, a test named 11, and 9 of those
  named a test the successor already declares. The attribute on the dead entry
  said nothing the live one did not already say.
- One of them said the opposite. `D-KNW-003` kept `provenance` apart from
  `binding`, and `D-KNW-005` revoked it and made the four vocabularies one enum.
  `KnowledgeTest::everyScopeInTheCorpusIsOneTheEnumDeclares` declared both. The
  test disproves the entry it stood on.
- Two named a test about something else entirely. A drawing's type size on an
  entry about the design system, an exclusion check on an entry about API
  stability. Neither would have caught anything the entry decided.
- The listing already separates them. A revoked entry sits in a run of its own
  under `Revoked, and kept as the record`. Mixed into the rest it looked like
  something to build on. A `coveredBy` beside it reads the same way.

## Decided

- `bin/cli decisions:check` fails and names `revokedBy` where the entry has one.
  So the reader learns where the attribute goes rather than only that it is
  wrong.
- The 16 attributes on the 11 entries went rather than moved. Nine were already
  on the successor. The other three held a claim about something else, and a
  move of one of those would be the wrong label `D-DOC-043`'s first **Assumed**
  describes.
- Decisions only. A requirement has no revoked state. A withdrawn one goes as a
  file, which takes its `heldBy` with it, so there is nothing for the same rule
  to hold there.

## Assumed

- That a test which held a revoked entry holds its successor, where one exists.
  Nine of eleven did already; nothing measures whether the two are the same
  claim.

## Wrong if

- A revocation drops the last guard on behaviour that is still there, because
  the successor never declared what the dead entry had. The check names
  `revokedBy` for exactly that moment, and nothing forces the move.
- An entry stays `open` to keep its tests. That would show as a statement nobody
  can read as true beside a green test. It is the reason `D-DOC-046` asks a
  title to say what is.
