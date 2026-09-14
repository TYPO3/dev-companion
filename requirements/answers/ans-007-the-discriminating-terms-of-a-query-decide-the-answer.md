---
id: R-ANS-007
title: 'The discriminating terms of a query decide the answer'
status: held
heldBy:
  - KnowledgeTest::aTermMatchesAWholeWord
  - KnowledgeTest::theDiscriminatingTermsOfAQueryDecideTheAnswer
  - ScopeTest::aRuleQueryIsPointedAtTheHintCorpusItBelongsIn
---

# R-ANS-007 — The discriminating terms of a query decide the answer

**The terms that separate one section from the rest score a query, not term
overlap.**

A word half the knowledge base carries decides nothing, and a term matches as a
word rather than as a substring. Which of the two corpora holds a subject, the
prose or the hints, is not the caller's problem. `typo3_rule_lookup` names the
hints that match the same query.

## From

"site set settings definitions" answered with the backend's Sass class names, at
a stated 75% of the query terms (2026-07-29).
