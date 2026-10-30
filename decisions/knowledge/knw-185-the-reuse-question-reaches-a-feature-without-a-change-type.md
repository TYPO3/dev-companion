---
id: D-KNW-185
title: The reuse question reaches a feature without a change type
date: 2026-10-30
status: open
coveredBy:
  - HintsTest::aFilteredListReachesExtbaseAndTheReuseQuestionOnce
---

# D-KNW-185 — The reuse question reaches a feature without a change type

**The `sitepackage` and `content-element` intents carry the reuse item of
`D-ANS-193` word for word, and the brief says it once. `extbase` opens with the
case it is built for and is reached by a filtered, paged list.**

## Evidence

- `feedback/2026-10-29-170000-no-intent-tells-a-session-to-search-the-extension.md`:
  two runs added a search and a contact form and never called
  `typo3_ter_lookup`. No task the maintainer wrote reached Extbase.
- `typo3_task_guide` on 2026-10-30, task "Add a list of tenders to the site
  that visitors can filter by ward, ten per page, from a table the sitepackage
  owns.": with `changeType: feature` the brief carried the reuse item of
  `D-ANS-193`. Without a change type it carried none. Neither brief reached
  `extbase`.
- `typo3/cms-form` and `typo3/cms-indexed-search` are system extensions on
  `.checkouts/12.4`, `13.4`, `14.3` and `main`, and Extbase ships
  `QueryResultPaginator` on all four.

## Decided

- The reuse item stands in the two intents a feature starts from, in the words
  of `TaskGuide::CHANGE_TYPE_CHECKLIST['feature']`. `TaskGuide` skips an intent
  item the checklist already carries.
- `extbase` states the filtered, paged list as its first case and is reached
  by the words a newcomer uses for it.
- `extension-reuse` names the core's two answers to a form and a site search.
- The card's trial run of a tender list is not run here. The maintainer chose
  on 2026-10-30 that a session here starts no forward run. The brief above is
  the route half of what that run would show.

## Assumed

- That the route was what failed. A session that reaches `extbase` and still
  builds the list without it would show the framing at fault.

## Wrong if

- A recorded run of a filtered, paged list reaches `extbase` in its brief and
  builds the list with a data processor and no pagination.
