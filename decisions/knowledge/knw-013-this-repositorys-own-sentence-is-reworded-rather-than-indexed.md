---
id: D-KNW-013
title: This repository's own sentence is reworded rather than indexed
date: 2026-08-02
status: open
coveredBy:
  - HintsTest::anAuditAskingAboutTestsReachesTheRuleAboutTheSupportedRange
  - HintsTest::settingTestsUpInAPackageReachesTheHintAboutThat
---

# D-KNW-013 — This repository's own sentence is reworded rather than indexed

**The conformance checklist's quality surface now names the test suite and the
supported TYPO3 versions it runs on. `extension-repository-layout` carries
`supported TYPO3 version` and `cross-version`. No sixth domain keyword joined.**

`D-KNW-009`'s first **Wrong if** asked what to do when a test question still
misses the PHP domain through a phrase nobody wrote down. It said the answer
would be another entry. The phrase arrived from inside. The checklist wrote its
own audit surface as "Quality: tests, the check layer, documentation,
deprecations, and upgrade readiness". The bare `tests` is the word that entry
rejected on purpose. An audit that asks in the checklist's own wording resolved
to the `docs` domain alone. The rule about the supported range was out of both
hops' reach. That is a matrix that resolves per supported version, the lowest
and the highest of each supported major.

What that cost is the feedback of 2026-07-31 19:36 UTC. A conformance audit
whose recommendation 19 was to "Consider typo3/cms-compatibility package for
cross-version testing", a package no covered line ships.

## Evidence

- The measure `D-KNW-009` names, repeated over the 107 texts this repository now
  has to hand, 41 scenario prompts and 66 hint titles. Plus the eleven test
  queries `HintsTest` and that entry hold. All four candidates ran against the
  same corpus with a diff against the same baseline.
- **A phrase in `Domains::KEYWORDS[PHP]`** for that sentence can only be the
  bare `test`, so that is what the measure covered. It moves three texts and
  buys nothing. The checklist query gains the PHP domain and still reaches
  neither test hint. "browser tests for the site package" loses
  `extension-repository-layout` from its answer and gains
  `sitepackage-initial-content`. The reweigh `D-KNW-009` rejected it for is
  smaller than it was, since `test coverage` is in the vocabulary now and
  `SKILL-01` no longer moves. The keyword still pays for nothing.
- **A statement about the supported range in `project-extension-tests`** moves
  nothing at all: none of the 107 texts, and neither of the two queries. It is a
  second copy of a statement the corpus already holds, and reach is not what it
  changes.
- **Vocabulary on `extension-repository-layout`** decides the second hop.
  `supported TYPO3 version` and `cross-version` move two texts and only towards
  the rule. `EXT-05` leads with the matrix rule and still returns the harness
  hint. It reads "wire them into our GitHub Actions so every pull request runs
  them against all supported TYPO3 versions". "does the test suite cover every
  supported TYPO3 version" earns it at `appliesTo(23)` instead of reaches it
  text-only in second place. Nothing else in the corpus moves.
- Any pattern with a form of the word *test* costs more than it buys. Three of
  them, `cross-version testing`, `test matrix`, `CI matrix`, lift the hint's
  whole `appliesTo` field against every test query. "how do I test my extension"
  then leads with a layout hint on a score tie that the title breaks. The two
  patterns that name a version rather than a test do not.
- **The checklist's own sentence** decides the first hop, and nothing else can.
  With the vocabulary in place, the original wording still returns `docs` and no
  test hint. Reworded to "the test suite and the supported TYPO3 versions it
  runs on", the same query resolves to `php, docs`. It leads with
  `extension-repository-layout` at `appliesTo(23)`. Through `typo3_hint_lookup`
  with an extension's paths it returns that hint first, ahead of the
  `scope: project` layout hint it answered with before.

## Decided

- The sentence got a rewrite rather than an index. A surface as one bare noun is
  what made the query unanswerable. A wider vocabulary to meet a word this
  repository chose pays for its own phrase twice. The keyword lists get longer
  and the sentence stays vague. The Quality surface is the suite *and* the
  versions it runs on, which is the half the feedback's audit never asked about.
- `D-KNW-009`'s **Decided** holds and is what the two halves are. The checklist
  sentence is the domain hop, the two patterns are the hint hop, and neither
  reaches the rule without the other.
- No sixth entry in `Domains::KEYWORDS[PHP]`, and the bare `test` stays out on
  the same measurement that kept it out before.
- No second statement of the range rule in `project-extension-tests`. Where a
  rule lives is a corpus question and this one has its answer. The matrix
  belongs with the repository whose lock file it stands in for. A copy that
  moves no query is only a second thing to keep true.

## Assumed

- An audit asks in the checklist's words. The skill tells it to write the
  surface list down from the checklist and then to query one surface at a time.
  So the sentence is the query. But nothing measures what a model actually sends
  and the reported audit is one session.
- `cross-version` is what a caller writes for the subject. It is the feedback's
  own word and nothing else in the 107 texts uses it. So it is a phrase with one
  witness rather than a measured one.

## Wrong if

- A conformance audit reaches the matrix rule and still recommends a package for
  cross-version tests. The reach is then not the failure and the rule's own
  wording is. That is a question for the hint rather than for the vocabulary.
- A question about the Quality surface for a project sitepackage gets an answer
  that leads with a distributed extension's layout. One supported version is a
  range of one, and the sentence stayed true for both. But the hint it now
  reaches is not, and `scope` is the only mark of that.
- A test question with no version in it starts to lead with the layout hint.
  That is the tie that rejected the patterns with *test* in them. The two that
  stayed are one corpus-growth away from the same score.
