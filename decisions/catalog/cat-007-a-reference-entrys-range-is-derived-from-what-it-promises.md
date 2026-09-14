---
id: D-CAT-007
title: "A reference entry's range is derived from what it promises"
date: 2026-08-23
status: open
coveredBy: []
readings:
  - 2026-08-28
---

# D-CAT-007 — A reference entry's range is derived from what it promises

**An entry names in `files` what its sentence promises, wherever the directory
alone would claim a major the shape is not on.**

`D-CAT-002` checked a worked example by the existence of its directory. That
passed on a branch where the directory was there and the layout the entry
describes was not. The entry it broke on now names four files. Nothing said what
decides whether the next one does.

## Evidence

- Read against `.checkouts/` on 2026-08-23. `Build/tests/playwright/e2e` is on
  13.4 and on 14.3, and `e2e/accessibility/modules.spec.ts` is on 14.3 and later
  only. The directory derives v13, the four files derive v14, and v14 is the
  range the entry's sentence describes.
- The other six entries are on exactly the majors their sentence is about.
  `theme_camino` on 14 and later, `styleguide` on 13 and later, `blog_example`,
  `fluid_styled_content`, `Build/phpstan` and `Build/php-cs-fixer` on all four
  branches. No needle corrects any of them, and none of them carries `files`.
- `CatalogCheck::missingFrom()` reads the path and every entry of `files` and
  returns the first one a branch does not have. So it names a branch that kept
  the directory and lost the shape rather than counts it as one that has the
  example. That reads
  `v13 has Build/tests/playwright/e2e and not e2e/accessibility/modules.spec.ts`.

## Decided

- An entry names files where the derived range would otherwise be wrong, not for
  every clause of the sentence. All seven sentences promise a shape, so "an
  entry names its shape in data" is all seven. A list that grows with every
  clause is the prose kept a second time in a form nobody reads.
- What no file covers stays prose somebody rereads. Whether `theme_camino` still
  demonstrates the layout is a judgement, and that half of `D-CAT-002` is what
  this entry carries on.
- `coveredBy: []`, because the check reads the four core checkouts and a unit
  test starts nothing on the machine (`R-COD-003`). `bin/cli catalog:check` is
  where it runs.

## Assumed

- The shape a sentence promises arrives with a file whose path somebody can
  name. Where it moves inside a file that stays, a spec that keeps its name and
  demonstrates something else, the needle is there and says nothing.

## Wrong if

- An entry's path and its files are all on the branch and what the sentence
  promises is somewhere else inside them. That is `D-CAT-002`'s **Wrong if** one
  level down, and the answer is the same one it named. The two or three files
  that carry the shape, this time inside the file.
- Somebody adds a needle to an entry whose range is already right, to assert its
  sentence. The list then grows toward a copy of the prose, and the check
  reports files where it should report ranges.
