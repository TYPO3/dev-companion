---
id: R-KNW-067
title: 'The e2e answer states the price of a Playwright-only change'
status: held
restsOn: [D-KNW-068]
heldBy:
  - KnowledgeTest::theE2eAnswerStatesThePriceOfAPlaywrightOnlyChange
---

# R-KNW-067 — The e2e answer states the price of a Playwright-only change

**The e2e entries state that nothing passes through to Playwright. So a change
to one spec costs the whole suite, and that run is what a review reports.**

Every e2e case builds its Playwright command from the project alone and reaches
no `"$@"`. `-s unit`, `-s functional`, `-s npm`, `-s composer` and `-s phpstan`
all hand on what follows `--`. So the passthrough a caller knows from the other
suites is absent here, and absent in a way nothing in the invocation notes says.
The note that describes `--` is for the suites that have it.

A session with a two-file diff in hand reads that gap as a question and answers
it with whatever the entry offers. The entry offered a local Playwright run.
That is a run outside the project's runner and therefore evidence about the
machine it ran on rather than a result.
`skills/typo3-core-patch-review/SKILL.md` says that in stronger words, and it
was the one place in the answer set that pointed the other way.

The price is what belongs beside the command. A session that reads "the whole
suite is what this costs" budgets for it in one step. One that reads a way
around it goes to look for the way around.

Where the local commands stay, they carry what they need. They run on the host,
where the browsers are an `npm --prefix=Build run playwright:install` of their
own. `Build/package.json` keeps that script beside `playwright:run` on every
branch that carries the suites. The containerised path runs the Playwright image
and never asks for it.

## From

A core patch review of a Playwright-only change, which took the printed local
command as the sanctioned way to run two specs. It spent roughly fifteen minutes
on a browser build that was not in the host cache. It produced no evidence at
all before the developer asked why it did not use the test suite
(`feedback/2026-08-13-214708`, 2026-08-13). A session read the passthrough on
`.checkouts/main`, `14.3` and `13.4`, and `playwright:install` on the same
three.
