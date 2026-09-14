---
id: D-ANS-040
title: 'A boundary guard is asked with a query that clears the floor'
date: 2026-08-03
status: open
coveredBy:
  - ScopeTest::whatARuleAnswerWithheldIsNamed
---

# D-ANS-040 — A boundary guard is asked with a query that clears the floor

**The test that holds a rule answer to name what it withheld asks a query whose
section clears the coverage floor by a margin. Not one that sits on it.**

Coverage is a share of the query's weight, and a term weighs by how few sections
carry it. So every section added anywhere in the corpus moves every query's
arithmetic. A fixture that passes at a hundredth above the floor is one entry
away from a failure on a corpus that got better.

## Evidence

- `whatARuleAnswerWithheldIsNamed` asked `review readiness for my site package`.
  The section it depends on, `Review Readiness`, withheld outside the core,
  covered 0.508 of that query against a floor of 0.5. Eight thousandths.
- `typo3-gerrit-workflow` gained four sections about the Gerrit push for
  `R-KNW-057`, and the same section fell to 0.462. Nothing about it changed. The
  new sections say `reviewers`, `Under Review` and `git-review`, so `review`
  went from 1.02 to 0.88 and `readiness` from 2.53 to 2.27. `site` and
  `package`, which that section never carried, rose with the corpus.
- To hold the old query green, the new prose would have had to give up the two
  words that are its subject. Those are the tracker status `"Under Review"` and
  the name of the `git-review` tool that reads `.gitreview`. Measured, that
  lands at 0.505 — eight thousandths again, in the other direction.
- `code style rules for my site package` reaches `Code Style` in the same
  withheld document at 0.612. It returns two kept sections from
  `typo3-commit-messages` beside it. It exercises the same path: a hit outside
  the core with a core-only document left out and named.
- The inside-the-core sibling, `insideTheCoreARuleAnswerWithholdsNothing`, is
  not on the floor, because its query reaches `Review Readiness` at 0.666, and
  it stays as it is.

## Decided

- The guard on withheld sections asks `code style rules for my site package`.
- The knowledge prose keeps the words of its subject. A corpus edited to keep a
  fixture above the floor answers worse for every caller. The next entry would
  have to edit it again.
- The margin is what makes a fixture a guard, so the docblock carries the
  measurement rather than only the query.

## Assumed

- That the test is about the shape of the answer and not about that one query. A
  hit outside the core says which core-only document it left out. The docblock
  says the first.
- That the query moved off is not the finding by itself.
  `review readiness for my site package` outside the core now returns the
  commit-message sections and names nothing as withheld. That is a thinner
  answer than it gave before, and no feedback has reported it.

## Wrong if

- A session outside the core asks about review readiness, gets the commit
  conventions alone, and concludes this server has nothing on the subject. Then
  the floor is what needs the attention, not the fixture.
- The new query drifts under the floor as the corpus grows. Then a fixture is
  the wrong instrument for this. The guard has to rest on what the answer
  contains rather than on what a query happens to weigh.
