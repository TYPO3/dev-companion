---
id: D-ANS-024
title: A rule reaches only the task that already names its subject
date: 2026-08-02
status: open
coveredBy:
  - LabelSearchTest::aCallerAboutToWriteAUnitIsToldItsSourceLanguage
---

# D-ANS-024 — A rule reaches only the task that already names its subject

**Every path to the source-language rule opens on a word the caller supplies. So
a content-element task that names no label never gets it.**

The words are `label`, `xlf` and `translation`. That is step 2 of the ladder,
queued as
[`R-ANS-015`](../../requirements/answers/ans-015-a-label-rule-reaches-the-task-that-never-names-a-label.md).

`feedback/2026-08-01-003313` is the second report of German written into a
default XLF as the source language. The rule it asks for landed two days before
its date, so it does not report an absent answer.

## Evidence

- The rule is here twice over, and both landed before the feedback. `72baa05`
  put "Write the source unit in English" into the `labels` intent of
  `knowledge/task-intents.json` on 2026-07-30. `8f0f589` put the same rule into
  the `language-files` hint the same day. `0e6cf08` added the correction to both
  on 2026-08-02. Step 1a is out, and `R-KNW-033` holds what landed.
- The two probes are the finding.
  `bin/cli hints:probe "add a testimonials content element to the sitepackage"`
  reaches `content-elements`, `sitepackage-layout` and
  `frontend-page-rendering`. It reaches no language hint at all. The same task
  with one word added — "add labels for a testimonials content element" —
  reaches `content-elements` and `language-files`.
- The hint that does fire says nothing about labels. `content-elements` runs to
  fourteen hints that cover what an element owns, `addRecordType`, the preview
  template and the record variable. None of them names an XLF file.
- The `labels` intent opens on the same words. Its `match` is xlf, xliff,
  trans-unit, locallang, translation and language file; its `matchWeak` is label
  and wording. `TaskGuide::answer()` runs `TaskIntents::detect()` over the task
  text and the area, and the paths of the call are not read for an intent.
- The one tool the session did call carries no such rule. `typo3_label_lookup`
  closes each answer with how a template references a label and where a caller
  may reuse it. Its empty branch adds the reuse boundary and nothing else. A
  caller reaches it exactly when they have established that no unit exists and
  are about to write one.
- A tool next to it states the attribute and declines the rule on purpose.
  `ExtensionScope` prints the `source-language` each file declares, then adds
  that this is "not what it should declare — typo3_hint_lookup owns that rule".

## Decided

- Step 2, delivery. The rule exists, has the form of a rule, and only a query
  this task had no reason to write reaches it. Not 1a, because it landed on
  2026-07-30. Not 4, because nothing here shows that the session read the
  wording and it failed to take.
- Queued rather than closed on the spot. The placement is the answer text of
  `typo3_label_lookup`, or the `content-elements` hint, or the words the
  `labels` intent matches on. The first is `src/`, which
  [judging.md](../../documentation/records/judging.rst) keeps out of a run that
  has read only this repository.
- Which of the three carries it is not settled here. Each is a different claim
  about who a rule belongs to, and a judgement ends at the diagnosis.
- [`D-KNW-011`](../knowledge/knw-011-a-rule-that-names-a-defect-names-its-correction.md)
  is not what fired. Its second **Wrong if** asks for a run that called
  `typo3_hint_lookup` and named no language file; this run called it not at all.
  That is the same gap one step further out, so this entry records it rather
  than counts it as evidence against that entry.
- The feedback's own suggestion is not adopted as written. It asks for the
  structure of the default file and its `source-language` attribute, and that
  half already stands written. What it needs and does not name is that the
  answer arrive before the caller knows to ask for it.

## Assumed

- That the session's task text named no label. Its debrief lists
  `typo3_label_lookup` among the tools it used, so it reached the subject at
  some point. Nothing records the words of the task. The probe contrast holds
  either way: it is a property of the corpus, not of that run.

## Wrong if

- A content-element task that never names a label gets the source-language rule,
  and a session writes German into a source file anyway. The rule would then
  arrive and the session would not take it, which is step 4 and a rewrite.
- The placement lands on `typo3_label_lookup` and a later report comes from a
  run that authored labels and never called it. The tool would then not be what
  every label passes through, and the hint is where the rule belongs.
- A run passes the XLF paths to `typo3_task_guide` and is still not offered the
  `labels` intent. Matching intents on the task text alone would then be the
  defect, and the match list a fix that leaves the path case open.

## Since then

The placement is the answer text of the label lookup. What settled it is which
of the three the sessions that reported it stood in front of. Both had called
that tool, neither called the one that is the only route to the intent, and a
third session reports the same. Neither asked for a hint, so the line would have
served a reader that never arrived.

This repository already tells its clients that the tool is the route. It says so
in a skill and in the base's own list of lookups a task starts with.
