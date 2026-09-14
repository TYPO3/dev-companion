---
id: D-SCO-004
title: 'The frontend is recognised by name'
date: 2026-07-29
status: revoked
---

# D-SCO-004 — The frontend is recognised by name

**A frontend marker with no backend marker withholds the `Backend CSS` and
`Backend TypeScript` categories, and the answer says which and why.**

The server answered the backend CSS hints for a Bootstrap 5 theme extension,
where every one of them points the wrong way. Treat Bootstrap as legacy, prefer
`--typo3-*` properties, work in both backend color schemes. The paths gave
nothing away. `Resources/Public/Scss/bootstrap.scss` and
`Build/Sources/Sass/_variables.scss` have exactly the shape of core paths, and
the second one is one.

## Decided

- The task text decides. A frontend marker with no backend marker withholds the
  `Backend CSS` and `Backend TypeScript` categories, and the answer says which
  and why. Scope::isOutsideCore was the obvious lever and is the wrong one: an
  extension's backend module has backend CSS, and the core renders a frontend.

## Assumed

- Words are enough here although R-SCO-001 says they are not for `outsideCore`.
  The difference is what a wrong answer costs. A withheld category leaves a
  caller with a pointer to docs.typo3.org. The wrong direction hands over four
  confident instructions to rewrite a theme that works.
- Naming the categories `Backend CSS` and `Backend TypeScript` is worth the
  churn in every rendered answer. A caller reads a category label on every hit,
  and a boundary notice only when it fires.

## Wrong if

- A core contributor who works on the frontend rendering of
  `fluid_styled_content` loses the CSS hints they wanted. `styleguide` and
  `backend` are the escape, and the notice names them.

## Revoked on `2026-08-02`

The withheld categories held and the sentence about the escape did not. Asked
with a `fluid_styled_content` CSS path and a frontend task, both sections go and
the corpus says the contributor loses nothing they wanted. The wrong part:
`styleguide` worked and no answer named it, while the escape the notice did name
addressed somebody who styles a backend module. The notice names both now, each
with the case it is for. A mechanism a word reaches is no escape while the word
appears in no answer this server writes.

## Since then

Tests hold both halves rather than argue them.
`ScopeTest::aCoreContributorOnFrontendLosesTheBackendUiSections` is the loss and
`ScopeTest::theNoticeNamesTheWordsThatBringTheBackendUiSectionsBack` is the
escape. The second asserts the clause in the notice and then calls with it. A
notice that offers an escape and a notice that apologises for one are the same
string to everything except a caller who tries it.

`R-SCO-004` carries the withheld categories, and nobody owes a successor
decision. It names the two categories as the backend interface's. It says the
answer withholds them with the reason where a task names the frontend, which is
this entry's statement. The two tests above are among the five that hold it.
