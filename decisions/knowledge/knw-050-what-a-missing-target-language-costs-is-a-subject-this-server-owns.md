---
id: D-KNW-050
title: 'What a missing `target-language` costs is a subject this server owns'
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aTranslationFileIsToldWhatAMissingTargetLanguageCostsIt
  - HintsTest::whatAMissingTargetLanguageCostsIsWithheldWhereItIsFree
---

# D-KNW-050 — What a missing `target-language` costs is a subject this server owns

**The corpus states that v14 reads a locale-prefixed XLF with no
`target-language` on `<file>` as the default language and discards its
`<target>` values.**

The labels then render in the source wording, and nothing raises, logs or
deprecates. `language-files` already names the attribute, inside the correction
of a source file that is not English. That is a procedure for an author, and an
audit does not author a file. It opens one. The conformance skill asks the
session to read every returned rule in both directions. This rule has no second
direction, because it never says what the file that lacks the attribute does.

## Evidence

- The TYPO3 claim holds, read in `.checkouts/14.3` at `faf60eea222`.
  `XliffLoader::parseXliff1()` opens on
  `$isDefaultLanguage = !isset($fileTag['target-language']);`, and the branch
  that guards takes `<source>` where the other takes `<target>`. Neither path
  raises, logs or deprecates anything.
- That loader is the one in play. `Configuration/DefaultConfiguration.php` maps
  `LANG/loader/xlf` to `XliffLoader`, while
  `Localization/Parser/XliffParser.php` carries
  `@deprecated will be removed in TYPO3 v15.0. Switch to Symfony Translation loaders`.
  The class whose name a session greps first answers a different question, which
  is what the session that reported found.
- The trap is new in v14. In `.checkouts/13.4` and `.checkouts/12.4` the same
  decision is `$this->languageKey === 'default'`, the language the caller asked
  for and not an attribute of the file. Those majors read a translation file
  with no `target-language` correctly and v14 discards it. So this is an upgrade
  finding as much as one for an author.
- The manual does not carry it. `Feature-107436-SymfonyTranslationIntegration`
  and `Feature-108049-ModernizedTranslationWorkflow`, the v14 changelog entries
  for the loader switch, name neither `target-language` nor `source-language`.
- Step 1a, and not 2 or 3. `bin/cli hints:probe` reaches `language-files` from
  the symptom, "a German translation file renders English labels", at
  `appliesTo(16) + text(60)`. It reaches it from the upgrade words, "extension
  upgrade to TYPO3 14 translations stopped working", at
  `appliesTo(11) + text(109)`. Delivery and routing work. The corpus has no such
  sentence to deliver. `target-language` occurs twice below `knowledge/`, in the
  `language-files` correction and in the same wording in `task-intents.json`,
  both about a source file in the wrong language.
- Not step 4. The rewrite
  [`D-KNW-011`](knw-011-a-rule-that-names-a-defect-names-its-correction.md)
  queued landed in `0e6cf08` on 2026-08-02, a day before this feedback, and the
  session read it. Its observation quotes the corrected wording back nearly
  verbatim. What it could not get from that wording is the consequence, and no
  rewrite of an author's procedure produces one.
- The corpus already writes a failure mode of this shape. `site-label-language`
  says of a `typo3Language` mismatch that it "silently falls back to the English
  source instead of reporting an error". That is the neighbour cause of the same
  symptom, stated the way this one is not.

## Decided

- Step 1a of the ladder, on the substance rather than on the wording, and queued
  rather than closed on the spot. The gap is a statement about TYPO3 with a
  version boundary on it. The run that judged this read three checkouts to
  establish that much. Where the statement has to reach is the research the todo
  owes.
- The priority is `normal` and the judgement is what set it. One session
  reported it, which is not the several that would earn `high`. What lifts it
  off the floor is that the failure is silent in the version every current audit
  targets. The session found it in installed source rather than in any answer
  here.
- Not step 1b. No shape of answer is absent. The same audit's
  `feedback/2026-08-03-164805` asks for a lookup that reads an identifier out of
  the installed packages. The four hops this feedback counts are that ask a
  second time. The card for `164805` carries it, and this entry does not fold it
  in. Such a lookup would have shortened the research and still never have told
  the session that the attribute decides anything.
- Where the XLF schema check belongs is the todo's rather than settled here. It
  is a question about the check layer `typo3-extension-testing` describes, and
  nothing read for this judgement says which answer is right.

## Assumed

- That this path really discarded the German file rather than another silent one
  in the same loader. The package is in another checkout and this run did not
  read it. The lever is the same either way, and there is a second such path
  beside it. `requireApprovedLocalizations` defaults to `true`, and the loader
  drops every unit marked `approved="no"` on the same lines, which the corpus
  does not state either.
- That a statement about what a wrong file does reaches a session that audits. A
  statement about how to write a right one did not. Nothing has measured it, and
  it is the premise the whole entry rests on.

## Wrong if

- The statement lands and a conformance run still passes a translation file that
  declares no `target-language`. Then the reach is what failed rather than the
  wording, and the answer was step 2. The hint is not where such a task passes.
- The behaviour turns out to be a defect core corrects rather than the contract.
  A v14 patch that falls back to the locale in the file name would leave this as
  a description of one patch level.
- XLIFF 1.2 does not in fact require `original`, `source-language` and
  `datatype` on `<file>`. The feedback asserts it and this judgement did not
  check it, so a schema check recommended on that basis would rest on nothing.

## Since then

Step 1a landed as two statements, both bound: what the absent attribute does to
the file, and that no schema check reports it.

A session settled the third **Wrong if**, and it holds the other way round than
the feedback read it. The specification makes three attributes required and this
one optional, so the file whose translations the loader discards is valid XLIFF.
The core check the feedback asked for exists. It validates those three and never
reads this one. So the check belongs nowhere, and the hint states instead what a
validator cannot see.

The reach holds with no change to a skill. What step 2 would be stands for
whoever finds the first **Wrong if** fired, and it is not in the queue. The
decision bets on the statement's reach.
