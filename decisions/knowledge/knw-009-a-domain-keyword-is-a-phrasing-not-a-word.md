---
id: D-KNW-009
title: A domain keyword is a phrasing, not a word
date: 2026-08-02
status: open
coveredBy:
  - HintsTest::settingTestsUpInAPackageReachesTheHintAboutThat
---

# D-KNW-009 — A domain keyword is a phrasing, not a word

**Testing reaches the PHP domain through phrasings, not through the bare word
`test`.**

The five are `test coverage`, `test suite`, `set up tests`, `write tests` and
`automated test` — what somebody with no suite yet says.

`Domains::KEYWORDS[PHP]` carried `unit test` and `functional test` and nothing
else. So a question about tests that had not yet reached a harness, "set up
tests for our site package extension", was Fluid and TypoScript work. Every hint
in `php.json` fell to the filter before any score.

## Evidence

- Measured over the 105 texts this repository has to hand: 40 scenario prompts
  and 65 hint titles. Nine carry the word "test" and two of them would change
  domain by it — `SKILL-01` and `SKILL-04`.
- The bare word came first in the trial and is worse than the gap. `SKILL-04`
  does not move. `SKILL-01`, "Review our site package's test coverage", went
  from `sitepackage-layout` to `sitepackage-initial-content` ahead of it, and
  reached no test hint either way. Term weights come from the candidates
  (`R-ANS-007`), so a wider candidate set reweighs every term in it. A keyword
  that only widens buys a different wrong answer.
- The phrases do move it, once `project-extension-tests` also carries
  `test coverage` in its `appliesTo`. `SKILL-01` leads with that hint, and so
  does the query this started from, with or without a path.
- What none of them moves: "test the frontend rendering of the page", "browser
  tests for the site package", "add tests for the DataHandler change". And the
  three other phrases `HintsTest` holds.

## Decided

- The domain vocabulary and the hint vocabulary grow together. A domain makes a
  category a candidate; what wins inside it is the hint's own `appliesTo`. A
  move of one without the other trades a miss for a wrong hit.
- No bare `test`. `Text::containsWord()` matches at a word boundary as a prefix,
  so `test` would also carry "testing" and "tests". The reach is not the
  problem, the reweigh is.

## Assumed

- A caller who has no suite yet says one of these five. It is the vocabulary of
  the case the domain lacked rather than of the case it already had. Nothing
  measures the phrases nobody in this repository has written down.

## Wrong if

- A question about tests still lands in Fluid because it uses a sixth phrase.
  The answer is then another entry rather than the bare word, and the
  measurement above is the one to repeat.
- The PHP category starts to win where the question is about the website. That
  is what the three unmoved phrases above are for, and what a fourth keyword
  would put at risk.

## Since then

The sixth phrase arrived out of this repository's own text. A checklist wrote
its audit surface down as the bare word this entry rejected, so an audit that
asks in that wording reaches no PHP hint at all. What it cost is on the record —
a recommendation to use a package no covered line ships, where the corpus
already held the answer.

The hint half is the second place it did not reach, and that one is inside the
domain. The harness hint carries ten test phrases and says nothing about a
supported range, while the hint with the range carries no test phrase.
`D-KNW-013` settles both with a reworded sentence and two new patterns that name
a version rather than a test. The bare word had a second measure and a second
rejection.
