---
id: D-ANS-003
title: Retrieval stays lexical and runtime inspection stays narrow
date: 2026-07-30
status: confirmed
coveredBy:
  - StructureTest::retrievalIsLexicalAndNothingHereOpensADatabase
---

# D-ANS-003 — Retrieval stays lexical and runtime inspection stays narrow

**No embedding dependency, no semantic index, and no generic SQL, log or
database-schema tool. What version, audience, binding and source decide is not a
gap a semantic match would close.**

The live-documentation source created the point at which a session could measure
the search and inspection gaps that remain instead of anticipate them.

## Evidence

- `bin/cli hints:coverage` finds every one of the 61 hints from its own title.
  Seven of 25 scenario prompts reach no architecture hint: `CORE-06`, `META-01`,
  `META-04`, `META-05`, `EXT-01`, `EXT-05`, `SITE-03`. They are respectively
  version spread, orientation, structured-only output, installation, an upgrade,
  the explicit testing boundary and effective runtime configuration. The route
  that owns them is not an architecture hint, so a semantic match would make the
  report look fuller and answer them no better.

## Decided

- This entry adds no embedding dependency and no semantic index. The concrete
  live-docs ranking defect was lexical: separated words tied a precise
  multi-word title. A weight on adjacent query terms repairs it, and
  `DocumentationTest` guards it. Semantic retrieval may nominate candidates in
  future, but version, audience, binding and source still decide what the answer
  may return.
- This entry adds no generic SQL, log or database-schema tool. No feedback and
  no new scenario needed one once live documentation and the installation
  diagnostics already there were available. A runtime tool starts with the
  session that could not finish without it, not with parity against another
  server.

## Wrong if

- ~~Real queries repeatedly miss a present section after short English
  alternatives.~~ Fired on 2026-08-02 on the changelog, where the cause was
  tokenisation. The miss it names is one two causes share, so it says nothing
  about whether retrieval is what has to change.
- A session cannot complete a scenario's diagnosis from project files, effective
  configuration and the caller's own checkout. Record that session; it supplies
  both the tool boundary and the safe result shape.

## Confirmed on 2026-08-02

The retrieval half fired, on the changelog rather than on the prose sections,
and the cause is lexical. Two sessions in different checkouts report it, and
both reproduce. Two file names reach no entry, while the same names split into
words reach the entries about them. The search reads the file name and its
CamelCase split, so a name written as one token matches nothing.

## Confirmed on 2026-08-02

The runtime half fired once in a recorded run, and what it asks for is narrower
than what this entry refused. A review left one rule unraised because it had not
verified each column against the schema analyzer's derived output. That is the
one diagnosis in either run its files, its configuration and its checkout could
not complete. The boundary is one table's derived columns, and the environment
has no database, so whether that class answers without one is what a card
settles.

## Since then

A session read the runtime half a sixth time on 2026-08-18. This is the first
read where the caller's own checkout could not complete the diagnosis. A package
declared three majors with one installed, and the checkout it had was at the
wrong one. It settled the other half with a curl of a raw file and then with a
second core installed.

So the **Wrong if** fired in the direction it names. What the session supplies
is not the boundary this entry refused: what it lacked is a branch rather than a
reader.

## Confirmed on 2026-08-22

Eight readings held the decision and changed nothing in it. Six are the runtime
half read and answered the other way. The caller's own checkout completed the
diagnosis, and what it lacked was a sentence rather than a source. The session
declined a detector because it would key on the wrong signal. Nothing deprecated
the API; an entry is dead because the path it names is gone. The session
narrowed what claimed too much instead, and `R-ANS-012` holds the files that
register when they run.

A session measured the retrieval half on the prose corpus, and the rank was
right. The page was reachable, and a word with nothing to do with the subject
decided each query.
