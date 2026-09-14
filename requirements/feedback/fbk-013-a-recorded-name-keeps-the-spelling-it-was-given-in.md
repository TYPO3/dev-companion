---
id: R-FBK-013
title: 'A recorded name keeps the spelling it was given in'
status: held
restsOn: [D-FBK-039]
heldBy:
  - FeedbackTest::aNameFromOutsideThisServerKeepsItsCapitals
  - FeedbackTest::aNameIsFoundHoweverItsSeparatorsAreSpelled
  - FeedbackTest::aRecordedNameKeepsTheSpellingItWasGivenIn
  - FeedbackTest::everyNameTheCorpusCarriesIsSpelledTheWayThisProjectSpellsIt
---

# R-FBK-013 — A recorded name keeps the spelling it was given in

**The channel writes a tool or skill name into a feedback as the caller gave it.
It reconciles the forms where it compares two of them.**

The name is what a search reads the corpus by. Half of that search is a grep
over the markdown rather than a call to `typo3_feedback_list`. A stored name
that no listing, no skill directory and no schema carries is reachable by the
filter alone.

## From

`typo3_feedback_record` called with the seven skill identifiers as the skill
listing spells them, with hyphens, stored `typo3extensionconformance`. That is a
name the project has nowhere, while the argument's own description asks for the
hyphenated one (2026-08-02).
