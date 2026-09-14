---
id: D-GUI-017
title: An issue the caller passed is written in either workflow
date: 2026-08-21
status: open
coveredBy:
  - CommitMessageTest::outsideTheCoreATrailerTheCallerWroteIsStillKept
  - CommitMessageTest::outsideTheCoreNoTrailerIsAddedAndNoneIsDemanded
---

# D-GUI-017 — An issue the caller passed is written in either workflow

**`Resolves:` and `Related:` carry the issues a call passed in either workflow,
and the wording says so.**

`D-GUI-010` measured the disagreement. A `project` call with `issue` returns a
draft with `Resolves: #348` above a last line that says the Forge issue does not
apply. A session in an extension repository read the parameters and concluded
the guide had no footer for the five pull requests its commit closed. It
committed without one.

## Evidence

- The trailer names are not the core's. `typo3/testing-framework` lives on
  GitHub with its issues there, and its history carries 74 `Resolves:` and 24
  `Related:` lines. `Resolves: #732` is in the newest of them, beside 9 `Fixes:`
  and 4 `Closes:`. Counted on 2026-08-21 in `.checkouts/testing-framework/main`.
- `Resolves: #348` is GitHub's own close form. GitHub documents `close`, `fix`
  and `resolve` with their inflections as close keywords, and states that a
  colon may follow the keyword. It closes the issue when a commit with one lands
  on the default branch. Read on 2026-08-21 from GitHub's "Linking a pull
  request to an issue".
- The behaviour was deliberate and already guarded.
  `CommitMessageTest::outsideTheCoreATrailerTheCallerWroteIsStillKept` has held
  it since the project workflow existed, so what was wrong is what the tool says
  about itself and not what it writes.

## Decided

- Nothing changes in the draft. The repair is the tool description, the two
  parameter descriptions, the output schema and the last line of the answer.
  Each of them said the Forge issue does not apply where it meant that none is a
  demand.
- `R-AUD-003` states the half it left out, because a requirement that says what
  a workflow does not demand is what the wording came off.
- The trailer names stay fixed. A repository whose tracker wants another word
  writes the message and passes it as `message`, and all its trailers stay. That
  is one path rather than a second vocabulary to configure.
- The parameters keep their names and stay Forge-shaped in the core. `issue` is
  a number in whichever tracker the workflow belongs to, so nothing changes its
  name for the audience that is not the core's.

## Assumed

- A TYPO3 extension repository tracks its issues where its code is.
  `typo3/testing-framework` is the witness here. A repository on GitHub whose
  issues are on Forge would want the Forge number under the same trailer anyway.

## Wrong if

- A caller reads `Resolves: #348` in a project draft as a claim that a Forge
  issue exists behind it.
- Repositories outside the core turn out to write something other than
  `Resolves:` and `Related:` for the issues their commits close. That happens
  often enough that one form cannot serve both audiences.
- A `project` draft comes back with a trailer the call never passed.
