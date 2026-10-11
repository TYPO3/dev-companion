---
id: D-ANS-179
title: A name with a dot or an underscore is matched whole
date: 2026-10-11
status: open
coveredBy:
  - DocumentationTest::aViewHelperTagReachesItsOwnPageAheadOfItsNeighbours
---

# D-ANS-179 — A name with a dot or an underscore is matched whole

**`TermSearch` keeps a query word with a dot or an underscore whole and matches
it as the word it is. It no longer cuts such a name to six characters and
matches the rest as a prefix.**

The stem served the forms of one word. A name has none, and cut short it was the
prefix of its neighbours.

## Evidence

- **Read over stdio on 2026-10-11.** `typo3_documentation_lookup` with
  `f:form.textfield` answered `f:form.textarea` first, at 13.4 and at 14.3. Both
  scored a coverage of 0.541 on the one term `form.t`, which `f:form.timePicker`
  carried as well. The order of the index broke the tie.
- **The same query set over the live index at 14.3, before and after.** The
  seven queries `D-ANS-032` ranks keep their ranks: 1, 1, 1, 8, 1, 1 and 6.
  `f:form.textfield` went from 2 to 1, `f:format.html` from 8 to 1, and
  `f:form.textarea` and `f:link.page` stay 1, now alone at the top.
- **The hints, before and after.** `bin/cli hints:coverage` reports the same. Of
  eight probes with such a name, six keep their first hint. `stdWrap_override`
  and `plugin.tx_news.settings` lose three text-only hits each, which they
  reached through `stdwra` and `plugin`, the cut and not the name.
- **The parts beside the whole were measured and rejected.** With the segments
  as terms of their own, `plugin.tx_news.settings` still reached nothing,
  because four more terms took its coverage under the floor.
  `Record API Fluid template access record.header` fell from 8 to 10.

## Decided

- `stem()` returns such a word unchanged, and `carries()` matches it with
  `Text::containsWord()`. The second half is the first one's consequence: whole,
  `format.html` was the prefix of `format.htmlentities`.
- A hyphen stays as it was. A hyphenated word is a compound of prose words, and
  their forms still share a stem.

## Wrong if

- A session reports a dotted path such as a TypoScript or TCA path that reached
  a hint before and reaches nothing now. Then the parts have to count after all,
  under a weight that keeps the coverage over the floor.
