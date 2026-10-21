---
id: D-ANS-183
title: A page of several ViewHelpers answers with the one the query names
date: 2026-10-21
status: open
coveredBy:
  - DocumentationTest::aPageOfSeveralViewHelpersAnswersWithTheOneTheQueryNames
---

# D-ANS-183 — A page of several ViewHelpers answers with the one the query names

**Where one page of the ViewHelper reference documents several ViewHelpers, a
search answers with the one the query names. Where it names none of them, the
answer is the page's own, whose tag the others extend.**

## Evidence

- [`feedback/2026-10-21-153139-search-for-f-form-select`](../../feedback/archive/2026-10-21-153139-search-for-f-form-select-returns-the-optgroup.md).
  `typo3_documentation_lookup` with `f:form.select` answered the Form.select
  page with the arguments of `f:form.select.optgroup`. The session needed a
  second call to read the select's own.
- `viewhelpers.json` of the 14.3 reference, read on 2026-10-21. It lists
  `f:form.select`, `f:form.select.option` and `f:form.select.optgroup`, all
  under the path `Global/Form/Select`. `Documentation` kept one entry per path,
  so the last of the three answered.
- Re-run after the change: `f:form.select` answers with its 17 arguments,
  `multiple` and `property` among them.

## Decided

- The index keeps every ViewHelper a page lists. The choice uses the query
  first and the shortest tag after it.
- The argument list stays in the excerpt, as `D-ANS-178` put it there. The
  session's second suggestion is what the corrected entry already answers.

## Wrong if

- A query names a child tag, `f:form.select.option`, and the search answers a
  section of the page rather than the page. Then the choice by the query never
  runs for it, and the section needs the same index.
