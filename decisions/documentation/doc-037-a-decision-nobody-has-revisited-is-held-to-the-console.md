---
id: D-DOC-037
title: A decision nobody has revisited is held to the console
date: 2026-08-18
status: open
coveredBy:
  - DecisionsTest::anUnvisitedDecisionNamesNoCommandTheConsoleLost
---

# D-DOC-037 — A decision nobody has revisited is held to the console

**The head of a decision that carries no dated section may name no `bin/cli`
command the console does not have.**

Three entries presented a deleted command as the way to do the thing. The only
thing that had ever checked a command name was a todo's `**Run:**` line.

## Evidence

- `Cli::knows()` exists and one place calls it, `TodoCheck`, for the `**Run:**`
  line of a todo. Nothing held a command named in a decision, in
  `documentation/` or in `AGENTS.md`. `LinksTest` holds paths and
  `ToolNamingTest` holds MCP tool names, and a CLI name falls between them.
- Swept on 2026-08-18, six command names written in the corpus were not
  registered. Three sat in revoked entries, which is the record at work. Three
  sat at the head of a live one. `bin/cli feedback:next` in `D-FBK-012`, which
  `D-FBK-016` deleted on 2026-08-02. `bin/cli todo:sync` in `D-FBK-016`, which
  `D-FBK-045` deleted on 2026-08-14. `bin/cli documentation:build` in
  `D-DOC-017`, which `D-DOC-020` folded away and `D-DOC-028` replaced with
  `documentation:prepare`.
- Two of the three had a visit and the visit did not reach the head. `D-FBK-016`
  says in its **Since then** that the sync went with `D-FBK-045`. `D-DOC-017`
  said the three commands became `documentation:render`, which itself went four
  days later. A dated section goes at the foot, and the head is what a reader
  reads first.

## Decided

- The head only — the statement and the paragraphs above the first section.
  Below it an entry is an account of the decision and the rejected options. The
  entry that removes a command has to name it there. `D-FBK-045` says the sync
  is gone, which is that sentence at its job. Held on the whole file it would
  fail on the entries that work.
- Only where no dated section stands. One of those is a return by somebody who
  wrote what changed, which is the mechanism this repository already has. It is
  also the looser half of this entry, and the reason is below.
- No statement is rewritten. A decision is what stood settled on its date.
  `D-FBK-012` and `D-DOC-017` got a **Since then** that carries the account to
  today instead, the shape `D-FBK-017` already used.
- A test rather than a command. The answer is binary and belongs where a rename
  that misses a reference fails the suite. That is what `bin/cli links:check`
  and `LinksTest` do for a path. Rejected: a `cli:check` beside them, which is a
  subject for one assertion.

## Assumed

- That a dated section means somebody read the head. It does not: two of the
  three found here carry one. What it means is that somebody was in the file,
  which is the closest thing to a signal there is. A hold on the head under a
  dated section too would demand a rewrite of statements.
- That the corpus outside `decisions/` can go unheld. `documentation/` and
  `AGENTS.md` have no head-and-evidence shape to separate a rule from an
  account. `documentation/records/readme.rst` names `bin/cli todo:sync`
  deliberately, and says what it did until 2026-08-14.

## Wrong if

- An entry gets a dated section for some other reason and its head then goes
  stale with no check. That is the first assumption as it fails. What would show
  it is a sweep that finds a head with a lost command under a **Confirmed on**
  about something else.
- A page under `documentation/` sends a reader to a command that does not exist.
  Nothing here would catch it, and the reader is somebody who follows a
  procedure rather than reads a record.
