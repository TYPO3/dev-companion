---
id: D-KNW-011
title: A rule that names a defect names its correction
date: 2026-08-02
status: open
---

# D-KNW-011 — A rule that names a defect names its correction

**A knowledge rule that calls something a defect also says how to correct it. A
caller with the defect in hand has to invent a correction otherwise.**

The language-files rule states the steady state: the source XLF is English, a
translation goes into the locale-prefixed file beside it. It adds that a
non-English source file is a defect to report. It never says what report it
recommends. A session on an audit of a sitepackage whose source files declare
`source-language="de"` therefore had two remedies open to it. Both read as
consistent with the rule.

## Evidence

- The rule arrives and the correction does not. `typo3_hint_lookup` ran against
  this branch with the feedback's task. "TYPO3 extension conformance audit: the
  sitepackage XLF files declare source-language=\"de\"; what is the correct
  source language convention and how is it corrected?". It returns the Language
  Files hints in full. Nothing in that answer says what to do with the file.
- Step 1a is out. `bin/cli hints:probe` on the feedback's own query reaches
  `language-files`, and the rule landed in `8f0f589` on 2026-07-30, a day before
  this feedback. `R-KNW-033` holds it and a test names it.
- The session that reported had the rule. Its sibling feedback of the same
  audit, `2026-07-31-193109`, lists "Settings.definitions.yaml labels — all
  German, no English source" among what it found. What it got wrong is only the
  remedy.
- Its own result offered both remedies at once: "Add en.xlf files with English
  source (or switch source to en and add de.xlf)". The second is the convention
  and the first is not, and the hint separates them nowhere.
- The TYPO3 claim holds, read in `.checkouts/14.3`. Every core source file under
  `Resources/Private/Language/` carries no prefix and declares
  `source-language="en"`. Locale-prefixed files exist for de, fr, da, ru and
  others; the only `en.`-prefixed XLF in the tree is a Fluid test fixture.
- Adding an `en.`-prefixed file does not correct the defect. `LabelFileResolver`
  reads the unprefixed file as the `default` locale, and `LocalizationFactory`
  sets `default` as the fallback of every locale. The German labels still
  surface wherever a locale has no translation of its own.
- The correction has a shape to state. A translation file declares
  `source-language="en" target-language="de"`, and each unit carries the English
  `<source>` beside the German `<target>` under the id the source file already
  uses.
- `typo3_extension_describe` is not the gap. It reports the `source-language`
  each file declares and says in the same answer that the rule for what it
  should declare belongs to `typo3_hint_lookup`.

## Decided

- Step 4 of the ladder, and queued rather than closed on the spot. The rewrite
  states an author's procedure, which is a statement about TYPO3 and has to hold
  on every branch this server covers.
- Not step 3. The route is a second question and does not answer this one. No
  wording anywhere below `knowledge/` names the correction, so a hint that
  arrived earlier would have left the same two remedies open.
- Not step 5. Nothing went out in trade here. The rule exists to stop new German
  labels and it does that. The package already in the wrong state is a case it
  did not serve.

## Assumed

- The session read the hint rather than answered from its own knowledge. That
  has no ground from here, and the lever is the same either way: this repository
  can name the correction and does not.

## Wrong if

- The corrected wording lands and a later run still recommends an `en.`-prefixed
  file. Then the wording was not what held the reader back, and the gap is step
  2 instead.
- The audit-shaped query is what fails.
  `bin/cli hints:probe "TYPO3 extension conformance audit of a site package"`
  reaches `sitepackage-layout` and not `language-files` today. So a session that
  never phrases its task around labels gets no offer of the rule at all. A
  second feedback of this shape, from a run that demonstrably called
  `typo3_hint_lookup` and named no language file, would move the answer to step
  2.

## Since then

The rewrite landed and the first **Wrong if** did not fire on the next run to
meet it. An audit a day later quotes the corrected wording back nearly verbatim
and recommends the wrong form nowhere. The second stays untested rather than
cleared, since that session named the file in its own call.

What the audit reports instead is the other direction of the same rule. The
correction says what a translation file declares and never what one without the
declaration does. So nothing in it fires on a file that is already wrong. That
is a gap rather than a wording failure, and `D-KNW-050` carries it.
