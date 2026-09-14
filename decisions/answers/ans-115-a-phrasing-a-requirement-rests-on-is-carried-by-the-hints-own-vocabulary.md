---
id: D-ANS-115
title: "A phrasing a requirement rests on is carried by the hint's own vocabulary"
date: 2026-08-27
status: open
coveredBy:
  - HintsTest::thePerClassDatabaseAnswerSaysWhatSurvivesTheRun
---

# D-ANS-115 — A phrasing a requirement rests on is carried by the hint's own vocabulary

**A wording something else depends on goes into that hint's `appliesTo`.
Coverage admission rests on weights every other hint in the corpus decides.**

The two ways past `MIN_COVERAGE` differ in what they are a property of. A
matched pattern is the hint's own and changes only when somebody edits that
hint. A coverage share comes from term weights over all the candidates, so every
statement anywhere in the corpus is one of its authors. A requirement that rests
on the second kind hangs on hints its writer will never read.

## Evidence

- `R-KNW-053`'s query "live database versus test database" stood at a coverage
  of 0.5016 against the floor of 0.500, with no `appliesTo` pattern matched. One
  further hint with "database" among the 108 candidates puts it under.
- That is what happened on 2026-08-26. An unrelated hint in
  `knowledge/hints/php.json` used the word "test", which took that term from 45
  carriers to 46 and its weight from 0.876 to 0.854.
  `HintsTest::thePerClassDatabaseAnswerSaysWhatSurvivesTheRun` failed. The
  statement that moved the weight was in neither the assertion nor the failure.
- The requirement's two other queries never reach the floor. "what happens to
  the test databases after the run" matches the pattern of the same name and
  "clean up functional test databases" matches "functional test". So both pass
  before the coverage question.
- No hint at all carries "versus", so it weighs `log(108) / 2` = 2.34, 35% of
  that query's weight, which nothing can ever cover. The floor had to clear a
  bar two thirds of the query had to reach.
- Swept over the 58 query literals `HintsTest` writes out. Coverage alone admits
  92 (query, hint) pairs, and three of them fall below the floor when one
  further hint carries one word of the query. Only `R-KNW-053`'s is a pair an
  assertion names; the other two are hints their query's assertion says nothing
  about.

## Decided

- `project-extension-tests` carries "test database". So the curated vocabulary
  admits the wording `R-KNW-053` rests on at 13, rather than a coverage margin
  of 0.0016. It is content rather than a repair: to tell a test database from
  the live one is what `D-KNW-022` wrote the statement for.
- "live database" was the other candidate and failed. A question about a
  production dump names it too, and it would reach a hint about setting up a
  test suite.
- Lowering `MIN_COVERAGE` was rejected. The floor keeps a hint that mentions a
  subject from an answer for it. A move for one query decides all 92 pairs above
  again.
- A new wording of the query failed. It is the wording the feedback behind
  `D-KNW-022` arrived in, and a test that asks an easier question than the
  caller does holds nothing.
- `bin/cli hints:probe` prints what each term of the query weighs. Per hit it
  prints which of the three ways in admitted it and how far the coverage stands
  from the floor. A session that has just written a hint reads there which word
  it made cheaper, which is what the failed assertion could not say.

## Assumed

- The 58 query literals are representative of what `HintsTest` holds. The sweep
  left out queries that reach `find()` through a variable or a data provider. A
  thin margin among them would look exactly like this one.
- Admission is the fragile part. A weight shift also reorders what passed. So an
  assertion that names the first hit can break while every hint still passes,
  and nothing here measures that.

## Wrong if

- A hint written into the corpus makes a reachability assertion in `HintsTest`
  fail again. The query it fails on is one an `appliesTo` pattern matches.
- "test database" brings `project-extension-tests` into an answer about a
  production database.

## Since then

A measurement of both **Assumed** on 2026-08-27 showed that neither held as
written, which `D-ANS-124` records. The queries that reach `find()` through a
variable, a data provider or a tool are 456 rather than the 58 literals swept
here. They are the same shape. 66 of the pairs they return fall out at one
further carrier, and no assertion names one of them.

The reorder half is the cheaper one rather than the worse one nobody measured.
No assertion in `HintsTest` breaks when a term picks up one further carrier.
None of the twenty that name a first hit breaks however cheap that word gets.
The curated vocabulary decides first place, in the tier above the score. So this
entry's statement stands as written. The fragile way past `MIN_COVERAGE` is
admission, and a wording something else depends on belongs in `appliesTo` for
that reason alone.
