---
id: D-KNW-022
title: The corpus states how long a per-class test database lives
date: 2026-08-02
status: open
---

# D-KNW-022 — The corpus states how long a per-class test database lives

**The corpus says every functional test class gets a database of its own, and
nowhere that the database is still there when the run ends.**

So a session reads the model and watches the databases appear. It has nothing
that tells it which of them the harness will reclaim and which are leftovers
beside the live one.

## Evidence

- The model half has an answer today. `bin/cli hints:probe` on the feedback's
  own query reaches `project-extension-tests` at `appliesTo(15) + text(79)`, and
  that hint states it outright. "Every test class gets a database of its own —
  the configured name plus a hash of the class — so that account has to be
  allowed to create databases. Under DDEV that means root rather than the user
  the site itself runs as."
- What the feedback reports about the name holds, read on the three refs
  [`D-KNW-002`](knw-002-a-hint-about-typo3-testing-framework-is-verified-against-tags-not-against-the-checkouts.md)
  pairs with the covered majors. `.checkouts/testing-framework/8` at 8.3.3, `9`
  at 9.6.1 and `main`. `FunctionalTestCase::setUp()` builds
  `$dbName = $originalDatabaseName . '_ft' . $this->identifier` on all three,
  and `getInstanceIdentifier()` is `substr(sha1(static::class), 0, 7)`. A
  `printworks_ft` plus seven hex characters is exactly that.
- Nothing drops one. The single `dropDatabase()` call in
  `Testbase::setUpTestDatabase()` runs at the *start* of a class's first test:
  drop where it exists, then create. `FunctionalTestCase::tearDown()` removes
  the site configuration directory and one cache file and nothing else. No ref
  declares `tearDownAfterClass()`. A test database therefore survives its run
  and goes only when that same class runs again.
- That half stands nowhere and reaches nothing.
  `bin/cli hints:probe "too many test databases left over"` and
  `"live database versus test database"` match no hint at all.
  `"clean up functional test databases"` reaches `project-extension-tests`,
  `project-repository-layout` and `extension-files`. The first of them on the
  sentence quoted above, which says what creates a database and not what becomes
  of it. A search of `knowledge/` and `skills/` for the `_ft` suffix or a
  per-class database finds that one sentence.
- The skill the session names is silent in the same way.
  `skills/typo3-extension-testing/references/phpunit.md` tells the session to
  "Select the functional database from existing project or CI infrastructure".
  It never says that the run makes more databases than the one it selected.
- The other half of the observation has an owner. That a record goes through
  DataHandler rather than a direct `INSERT` stands on `datahandler-persistence`.
  It says "which is what makes DataHandler the right way to seed and a direct
  INSERT the wrong one". It is the whole subject of the `003216` sibling that
  [`D-FBK-021`](../feedback/fbk-021-a-summary-feedback-is-judged-against-its-series-not-on-its-own.md)
  maps.

## Decided

- The feedback is **trimmed**, not closed. The second run above answers its
  model half, its seed half is `003216`'s, and what stays open is the lifetime
  of what the harness creates. One card exists for that.
- Step 1a on the lifetime rather than step 2. The feedback's own query reaches
  the sentence that would carry the clause, so neither placement nor delivery
  explains the gap. The clause does not exist.
- Not closed on the spot, although this run did read the package. A statement
  about `typo3/testing-framework` goes on a card whatever its size, and where
  the clause lands is a measure this run did not make. `project-extension-tests`
  is 981 words against the 544-word ceiling
  [`D-ANS-025`](../answers/ans-025-a-query-a-hint-carries-whole-is-not-diluted-out-of-it.md)
  measured. That is the tension
  [`D-KNW-019`](knw-019-the-corpus-states-that-a-functional-test-sees-only-what-it-primed.md)
  declined to resolve there.
- The primed-database premise has no restatement here. It is `D-KNW-019`'s
  statement, its third **Wrong if** is this feedback with it a second time, and
  the card below names the lifetime only.
- The feedback is not archived. One todo serves it, which is
  [`D-FBK-017`](../feedback/fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)'s
  invariant held rather than a state left behind.

## Assumed

- That a session which had read the clause would have kept the two apart.
  Nothing measures it, and the same session reports that it called nothing until
  the user asked it to.
- That what the harness leaves behind is inside the boundary. `doesNotCover`
  sends "running an installation" to the manual. The sentence that states the
  model already reaches into DDEV to say which account may create a database. So
  the lifetime of what the test harness creates counts as the harness's subject
  rather than the installation's.

## Wrong if

- The lifetime is narrower than one clause. A suite that runs against a
  throwaway database service, and a class with `$initializeDatabase = false`,
  each leave nothing behind. So "a run leaves one database per test class" is
  too strong as it stands.
- The clause goes into `project-extension-tests` and costs the hint the queries
  that reach it today. It is past the ceiling before the clause arrives, and the
  sentence that states the model is one of the hint's reachable ones.
- The clause states the lifetime and still does not close the gap. What cost the
  session was the difference between a test database and the live one. The `_ft`
  suffix is what answers that, and a clause written without it is the same gap
  one sentence longer.

## Since then

The clause landed and settles the first and third **Wrong if** in its own text.
It opens on the suffix that tells a leftover from the live database and names
the two cases that carry no such name.

The second did not fire. The hint is shorter than when this entry measured it.
So the clause arrived in a hint that has since got shorter rather than longer,
and the query still reaches it. One form reaches nothing at all, which is a
reach question rather than an absent statement.
