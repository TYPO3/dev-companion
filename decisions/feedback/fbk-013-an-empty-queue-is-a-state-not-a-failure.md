---
id: D-FBK-013
title: An empty queue is a state, not a failure
date: 2026-08-02
status: confirmed
coveredBy:
  - CliTest::theSightingsWaitForAnEmptyQueue
  - CliTest::whatIsAskedForOneOfSeveralSessionsIsNeverTheQueue
  - StructureTest::noTestSkipsItselfInsteadOfHolding
  - TodoTest::everyTodoAnswersForSomethingThatCanStillBeRead
---

# D-FBK-013 — An empty queue is a state, not a failure

**A test that needs a queued todo writes one, and nothing in the suite asserts
that this repository has a queue.**

The queue is the one directory here that a commit exists to empty. What the
three cases that held it non-empty produced was a red suite on the session that
finished the last todo. That is the one session that had done nothing wrong.

## Evidence

- The run of 2026-08-02 that finished `todo/540`, the bond where `R-FBK-007`
  rested on the revoked `D-FBK-005`. The file's removal left the queue empty and
  `composer test` red on three cases.
  `CliTest::theSightingsWaitForAnEmptyQueue`,
  `CliTest::whatIsAskedForOneOfSeveralSessionsIsNeverTheQueue` and
  `TodoTest::everyTodoAnswersForSomethingThatCanStillBeRead`, each of which
  opened with `assertNotSame([], Todo::items())`. The third said what all three
  meant: "nothing is queued, which is a state this can be in but not silently".
  Two PHP warnings came from the same emptiness, where the queued fixture read a
  position off `$items[count($items) - 1]` and found key -1.
- The commands had the case and the suite did not. `bin/cli todo:next` reaches
  the sightings only with the queue empty, which is the whole of what
  `**Every:** session` means. It closes with "Nothing is due and nothing is
  queued" where even those report no work. `bin/cli todo:list` prints "The queue
  is empty." and `bin/cli repository:check` counts it as `0 queued, 0 problems`.
  So three commands announced an empty queue and one failed on it.

## Decided

- The three guards go. Two of them are about what happens while items wait, so
  they write the item they need. `tests/Support/QueuedTodo` puts one at the end
  of the queue and takes it away afterwards. The third needed nothing. Its loop
  covers `recurring/`, `progress/` and `waiting/` besides. What is never empty
  is what recurs, because nobody deletes a recurrent todo.
- A precondition that is a state rather than a property of this checkout gets a
  setup, not an assertion. `StructureTest::noTestSkipsItselfInsteadOfHolding`
  already closed the other road out, and for the same reason in reverse. A test
  may not quietly cease to hold anything either. A setup of the state is the
  third answer, and it is the only one that leaves the case as it says it.
- The fixture moves out of `TodoTest` into `tests/Support/`, where `Directory`
  and the two installation traits are. It was one copy in one file and now
  serves two, which is the shape `Directory` came out at, after five copies had
  drifted.
- Nothing new arrives to say the queue is empty. The alarm was not absent, it
  was in the one place a session could not act on it. A check a commit cannot
  pass is not a report, and AGENTS.md already holds that nothing may fail on a
  state that is legitimately unfinished.

## Assumed

- That fixtures in the real `todo/` are right. It is what `TodoTest` has done
  since claims existed. The properties under test, the order `todo:next` reads,
  a claim, a release, are properties of that directory. A temporary root is not
  cheap here. `Paths::root()` comes from where the autoloader physically sits,
  which is the same thing that made a shared `vendor/` impossible under
  `D-FBK-010`.
- That the queue refills. An empty one hands over a sighting whose whole output
  is new entries. 67 feedback awaited a judgement on this entry's day, so the
  mechanism had plenty to work with. Nothing has yet run a session where it
  produced nothing.

## Wrong if

- A fixture outlives its run and goes out as real work. It carries a marker and
  an `#[After]` removes it, but a fatal error is not an `#[After]`. To
  everything that reads `todo/` the file is an ordinary queued todo.
- The queue stays empty and nobody notices. Nothing fails on it now by design,
  and what says so instead are three commands a session has to run. That is
  exactly the argument the sighting-before-the-queue order lost under
  `D-FBK-012`, made from the other side.
- A case arrives that needs the queue to be *empty*. Then this fixture is a
  precondition in the way rather than one that holds. The pair has to be able to
  take the queue away as well as add to it.

## Confirmed on 2026-08-22

The state happened again in the session that read this. The queue emptied, the
suite stayed green, and the three commands the entry names said so. The second
**Wrong if** did not fire, and the reason is the order rather than the alarm. A
session that empties the queue gets the sighting from the same command it worked
from.

The fixture holds where it stands, and nothing has needed an empty queue as a
precondition, which is the third **Wrong if**.
