---
id: D-DOC-058
title: 'A recording is reported against the day its sources moved'
date: 2026-08-26
status: open
coveredBy:
  - ToolAnswersTest::everyRecordedPageSaysWhichDayItWasAnsweredOn
  - ToolAnswersTest::theDayTheSourcesMovedIsWhatGitSaysOrNothing
---

# D-DOC-058 — A recording is reported against the day its sources moved

**`bin/cli tools:check` names how many recorded pages predate the last change to
`knowledge/` or `src/` in this repository, and fails on none of them.**

`D-DOC-006` decided that nothing checks the recorded half of a tool page. Its
second **Wrong if** is a recording that ages with nothing that asks for a new
one. This is the asking.

## Evidence

- The gap is not a worry. `D-DOC-016` measured it in the corpus on 2026-08-04.
  Two derived pages had their record from 2026-08-03 and `knowledge/` had moved
  since, so both moved on the next derivation.
- Measured in this worktree on 2026-08-26. 17 of the 18 recorded pages were
  older than the last commit that touched `knowledge/` or `src/`, the oldest by
  two days.
- The pages carry three different days already, because `tools:record` takes a
  list of tools and narrows to it.
- The drift `ToolAnswersTest` can already see is the shape: it holds every
  recorded answer to the schema its tool declares (`D-DOC-012`). What no check
  reads is the content: a hint rewritten or a sentence rephrased moves an answer
  and leaves every assertion green.

## Decided

- **The report reads two days, and calls nothing.** The day a page carries in
  its own first sentence, against the day git says `knowledge/` or `src/` last
  moved. Both are readable in any checkout, so the report needs no installation,
  no host and no `.checkouts/`. That is what `D-DOC-016`'s third **Wrong if**
  asks of this command.
- **`knowledge/` and `src/` together.** The first is what the answers consist of
  and the second is what composes them, and a commit to either can move an
  answer.
- It changes no exit code. A recording is evidence about a day, and the machine
  that could produce a new one is not the machine CI runs on, `D-DOC-006`.
- It sits in `tools:check` rather than in `unresolved:list`, which reads
  `requirements/` and `decisions/` and would gain a third subject. The reader
  who can act on this already looks at the surface, and
  `bin/cli repository:check` runs `tools:check` either way.
- Rejected: a second answer to the calls and a comparison. That is
  `tools:record` without the write, and it needs the checkouts and the fixture
  console, the growth `D-DOC-016`'s third **Wrong if** names. Four tools also
  answer from a host that moves without any commit here, so the comparison would
  report drift no session caused.
- Rejected: a day per page in the report. A new recording is one command over
  the whole tree, so a verdict per page answers a question nobody can act on per
  page. The count and the oldest day are what decides whether to run it.
- The report says nothing where git cannot answer. A checkout without history is
  a question that was never put, and a page is not behind because nobody could
  ask.

## Assumed

- That whoever merges runs `bin/cli tools:check` or `bin/cli repository:check`.
  Nothing makes them, which is the same assumption `D-DOC-006` makes about the
  reader who re-runs the command.
- That a day is granular enough. A recording and a commit to `knowledge/` on the
  same day read as current here, whichever came first.

## Wrong if

- The report says the pages are behind on nearly every run, because almost every
  branch touches `knowledge/` or `src/`. Whoever merges no longer reads it. Then
  the predicate is too coarse and the need is a per-tool one, which costs the
  calls this entry rejected.
- Somebody runs `bin/cli tools:record` to clear the report on a branch that
  changed one sentence. That is the commit `D-DOC-034` refuses for a different
  reason, and the todo behind this entry said it belongs to whoever merges.
- A recorded answer goes stale from something neither `knowledge/` nor `src/`
  holds, the manuals, the tracker, a package a checkout carries. The report
  calls those pages current.

## Since then

The two days came from different clocks, so a recording made in Europe after
midnight local reported itself behind sources it was newer than. `D-DOC-059`
settles both on the UTC day of an instant, and nothing this entry decided
changed with it.

