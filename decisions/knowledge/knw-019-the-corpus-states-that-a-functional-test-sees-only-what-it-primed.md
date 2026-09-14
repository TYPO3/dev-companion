---
id: D-KNW-019
title: The corpus states that a functional test sees only what it primed
date: 2026-08-02
status: open
---

# D-KNW-019 — The corpus states that a functional test sees only what it primed

**A functional test's database holds nothing but what that test imported. The
corpus states every fixture rule and never states that premise.**

The premise goes to the queue as a statement of its own, beside the rules that
rest on it. A session that does not hold the premise reads the fixture rules as
a convention it may also achieve another way. It then fetches records nobody
primed, and the failure looks like a broken query rather than an empty table.

## Evidence

- Of the three things the feedback names, two have an answer today.
  `bin/cli hints:probe "correct request type in a functional test"` reaches
  `project-extension-tests`, which states the request type outright.
  `$this->executeFrontendSubRequest(new InternalRequest($uri))`, with the
  cache-hash conditions of a hand-built query string beside it. `DataHandler`
  against a direct `INSERT` stands on `datahandler-persistence`. It ends on
  "what makes DataHandler the right way to seed and a direct INSERT the wrong
  one".
- The fixture rule stands too, on `core-tests`. "State is set up and asserted
  with CSV fixtures, not hand-written inserts", with `importCSVDataSet()` in
  `setUp()` and `assertCSVDataSet()` for the result.
- The premise under that rule stands nowhere.
  `bin/cli hints:probe "empty database per test run"` reaches
  `datahandler-persistence` on text alone and neither test hint. A search of
  `knowledge/` and `skills/` for an empty, truncated or primed database finds
  one sentence, on `project-extension-tests`. It is about the file-backed caches
  that survive the truncation, what outlives a test rather than what a test
  starts with.
- The skill the session names is silent in the same way.
  `skills/typo3-extension-testing/references/phpunit.md` asks for fixtures that
  are "minimal, deterministic, and explicit about their expected result" and for
  a reset of state that survives between tests. Neither sentence says the
  database begins with nothing in it.
- The premise holds and a reader can see it precisely, which is what makes it a
  statement rather than a guess. On `.checkouts/testing-framework/9` at tag
  `9.6.1`, `FunctionalTestCase::setUp()` builds the instance and the schema for
  the first test of a class. For every test after it, it calls
  `initializeTestDatabaseAndTruncateTables()`; the comment above `$isFirstTest`
  says so in those words. `$initializeDatabase = true` is a property a test
  class may turn off.
- Where the statement lands is a question the word counts already narrow.
  `core-tests` is 212 words and its `appliesTo` already carries
  `importCSVDataSet`, `dataset` and `csv`. `project-extension-tests` is 947,
  which is past the 544-word ceiling `todo/progress/2026-08-02-163000` measured,
  so text added there answers fewer queries than it costs.
- The other halves of this feedback have owners.
  [`D-FBK-021`](../feedback/fbk-021-a-summary-feedback-is-judged-against-its-series-not-on-its-own.md)
  maps the series: the live-database inserts are the subject of `003216`, the
  per-class test databases and the lost track of manual edits are `003929`. That
  nothing reached this session at all is `003356` and `003533`. They report that
  no skill activated and no lookup ran before the user asked for one.

## Decided

- Step 1a of the ladder, and queued. The ladder ran on the premise, not on the
  rules. The rules are all here, which is why what lands is one statement beside
  them rather than a hint of its own.
- Not closed on the spot. The statement is about `typo3/testing-framework`
  behaviour, so the run that writes it reads the package across the covered
  lines. This run read one tag of one line.
- Nothing comes from the request-type half. The query above is the second run
  that answers it. Its cost to the session belongs to the two feedback that
  report why no call happened.
- No card arrives for a DataHandler seed or for the test databases. Both are a
  sibling's whole subject, and a second card for one step is the overlap
  `bin/cli todo:claim` warns about.
- The feedback is not archived. The primed-database half is real and open, one
  todo serves it, and that is
  [`D-FBK-017`](../feedback/fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)'s
  invariant in force rather than a state left behind.

## Assumed

- That the premise belongs on `core-tests`, beside the fixture rule it explains.
  The alternative is the hint a project session actually arrives at.
  `project-extension-tests` opens with the statement that the conventions of a
  core test hold there too.
- That the premise stated would have changed this session. Nothing measures it.
  The same session reports that it called nothing before it was told to, so a
  premise it never read would have failed the same way.

## Wrong if

- The research finds the premise is narrower than one sentence. A class with
  `$initializeDatabase = false`, a snapshot through `withDatabaseSnapshot()`, or
  a record the framework imports on its own. Each would make "only what the test
  primed" too strong as it stands.
- The statement lands on `core-tests` and a session at work on a project
  extension never reaches it. The gap was then a placement, step 2, and the hint
  it landed on is the evidence.
- `003929` writes the same premise while it states the per-class database model.
  The corpus then carries it twice, in two hints, and neither says which is the
  one to correct.

## Since then

The statement landed the same day, and that commit settled the first and third
**Wrong if**. The package had its read on all three release lines, the premise
holds on each. The two cases that would have made it too strong are in the
sentence rather than left out. It is on one hint and nowhere else.

The second is the placement and is still open. Measured here, the call composed
by somebody who knows what to look for returns it. What would settle it is a
session that reaches it with no prompt, which is a forward run rather than a
read.
