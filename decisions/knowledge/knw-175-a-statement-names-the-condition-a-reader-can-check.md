---
id: D-KNW-175
title: A statement names the condition a reader can check
date: 2026-10-28
status: open
coveredBy: []
---

# D-KNW-175 — A statement names the condition a reader can check

**A statement in `knowledge/` names the measurable condition rather than the
mechanism that usually meets it, and a list says whether it is whole.**

A reader satisfies a mechanism by its use and a condition only by its result. A
sitepackage put its focus ring behind `:focus-visible`, as the statement asks.
The ring then reached 1.10 against two of the surfaces it lands on. A partial
list fails the same way from the other end: the reader takes it as complete and
stops the check.

## Evidence

- `feedback/2026-10-28-080712`, `claude-opus-5`, from
  `/home/lina/projects/typo3/projects/jambo-chep`. It names the shape and two
  instances of it, and asks for the rule rather than for the two rewrites.
- The two instances are each a feedback of their own from the same checkout on
  the same day. `feedback/2026-10-28-080312` asks for the focus indicator's own
  contrast. `feedback/2026-10-28-052314` asks for the third stale idiom in
  `content-elements` and for a word on whether the list is complete.
- The statements stand as the feedback quotes them. `typo3_hint_lookup` was not
  callable in this session, so the read went to the files it answers from.
  `css-accessibility-states` in `knowledge/hints/backend-css.json` says
  "Keyboard focus must be visible with :focus-visible or an equivalent pattern".
  `content-elements` in `knowledge/hints/content-elements.json` names the
  `addPlugin()` arguments and the `list_type` detour, and says nothing about
  whether more is stale.
- No statement below `knowledge/` names a contrast ratio. A search for `3:1`,
  `4.5:1` and `non-text contrast` matches nothing there.
- The corpus already writes the second half in places.
  `knowledge/documents/core/contribution/changelog.md` says "the four types
  below are the whole list" and, of the title prefixes, "the whole list".
- Nothing said the rule. `D-KNW-011` asks a rule that names a defect to name its
  correction. `D-KNW-043` asks a rule to carry its strength and its source.
  Neither says what a reader checks to know the rule is met.

## Decided

- Step 4 of the ladder, applied to the corpus rather than to one hint. The rule
  goes into `documentation/server/knowledge-base.rst`, beside `D-KNW-011` and
  `D-KNW-043`, where a session that writes a statement reads it. That page
  describes this repository, so the change closes on the spot and the feedback
  goes to the archive.
- The two instances stay with the two feedback that report them. Each needs a
  read outside this repository: the WCAG criterion for the focus indicator, and
  the v14 tab labels in `.checkouts/`. A rewrite here would carry a recalled
  ratio with a verified entry's authority. Each card points here, so the session
  that writes the statement words it as a condition.
- A mechanism stays in a statement where it is the fact. `:focus-visible` is
  still the selector a stylesheet takes. The statement adds the condition the
  selector has to meet, and the mechanism alone stops at being the whole claim.
- No sweep of the corpus for mechanism-shaped statements. Nothing tells one
  apart from a statement whose mechanism is the fact, so a sweep is a read of
  every hint, and no feedback but these two has reported the cost.
- `coveredBy` stays empty. No check reads whether a sentence names a condition,
  and a counter on words such as `must` would report the statements that are
  right.

## Assumed

- That the two instances are the shape rather than two accidents of wording. Two
  of the twelve feedback from one checkout on one day carry it, from one model
  and one task.
- That a session reads a condition as a check to run. The feedback says the
  ratio is arithmetic over two declared values. Nothing measured a session that
  got a statement worded so.

## Wrong if

- A statement rewritten as a condition, such as the focus indicator's contrast,
  arrives and a later run still ships what fails it. Then the wording was not
  the lever, and the check belongs in a skill step rather than in a sentence.
- A third feedback reports a list in `knowledge/` that a reader took as
  complete, after this rule stood. Then a rule a writer has to remember does not
  hold the corpus, and the need is a field on the list rather than a word in it.
