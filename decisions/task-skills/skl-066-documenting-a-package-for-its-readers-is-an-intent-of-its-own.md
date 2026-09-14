---
id: D-SKL-066
title: Documenting a package for its readers is an intent of its own
date: 2026-08-19
status: open
coveredBy:
  - KnowledgeTest::aBriefNamingOneKindOfWorkConfirmsThatKindAndNoOther
  - ScenariosTest::aCasesOwnPromptConfirmsTheIntentItIsWrittenAbout
---

# D-SKL-066 — Documenting a package for its readers is an intent of its own

**`documentation` is an intent beside `changelog`, and the `documentation`
needle belongs to it.**

One intent whose whole checklist is the core's release artifact reached
`typo3-extension-documentation`. So a request to document a package arrived at
the right workflow behind four items about
`typo3/sysext/core/Documentation/Changelog/`.

## Evidence

- **The half `SKILL-07` exists for reached nothing.** Measured on 2026-08-19
  through `TaskIntents::detect()`. "Once it works, document the public workflow
  for the extension's maintainers and editors in the right place" confirmed
  `backend-module` and no documentation intent at all. The `documentation`
  needle does not match the word `document`. `Text::containsWord()` ends a
  curated needle where its word ends (`D-ANS-050`), and `-ation` is not one of
  the inflections it may run into.
- **And the case that did reach it got the core's checklist.** `SKILL-03`,
  "Review and improve our site package's documentation for integrators and
  editors", confirmed `changelog`. Its four items name the changelog directory,
  the four entry types and the skeleton a type owes. The skill behind it was
  right and everything the brief stated about the work was the core's.
- **The intent already said so in its own title.** "Changelog or ReST
  documentation" is two kinds of work under one checklist, and only the first of
  them is what the items describe.
- **Measured after the change, over every contract case and forward review.**
  Two lines move and no other: `SKILL-03` confirms `documentation` where it
  confirmed `changelog`, and `SKILL-07` confirms `documentation` beside
  `backend-module`. This entry held the eight needles against the same corpus
  before they went in. `readme`, `manual`, `user guide`, `changelog` and `rst`
  reach no prompt there at all. `manual` stayed out because it names the
  adjective in "manual action" as readily as the artifact.

## Decided

- **A row of its own rather than a wider `changelog`.** One intent carries one
  checklist, and the two checklists share no item. An entry a release carries is
  a file with a fixed skeleton in a fixed directory. A manual is a surface
  chosen for a reader. Wider needles would have handed the first to whoever
  asked for the second, which is the cost `D-SKL-051` measures a wider net
  against.
- **The skill's age answers the third warning in `D-SKL-013`, since the skill is
  older than the row.** What it warns against is a name invented to fill the
  table. `typo3-extension-documentation` is out, describes this work in its own
  front matter, and is what two written contract cases ask for. The row is the
  route to a workflow that already exists rather than a workflow invented for a
  route.
- **The `documentation` needle moves, and the verb comes with its object.**
  `document the`, `document our`, `document this`, `document how`,
  `documenting the`, `documenting our`. That is the treatment `audit`'s
  `reviewing` got the same day, so the word reaches the intent only where its
  object follows it. `readme` goes in as the second name for the artifact;
  `changelog`, `rst` and `release note` stay where they are.
- **`changelog` keeps `typo3-extension-documentation` outside the core.** That
  workflow writes an extension's release notes, and nothing measured says
  otherwise. What it loses is the manual guide, which describes a
  `Documentation/` directory rather than an entry in one.

## Assumed

- That a caller writing about a manual writes one of these eight words. The two
  measured prompts carry `documentation` and `document the`. The other six are
  the same word in the forms a request takes, and no filed session carries this
  task shape in its own words.
- That the checklist is what a documentation task lacks. It states what the
  skill's own checklist reference states first, the surface, the audience split,
  the public boundary. It does so for the caller that arrives without the skill,
  which is the caller `D-SKL-013` serves.

## Wrong if

- A task about a core changelog entry confirms `documentation` and gets the
  manual items. Then the split is on the wrong side of the words, and what the
  two intents disagree about is the artifact rather than the verb.
- A filed session documents a package into the project's README with the brief
  in front of it. Then the second item is the one that had to be a hint the
  session reads whole, not a sentence in a list.
- `readme` reaches a task that is about the repository's own readme file rather
  than about a package's documentation. It reaches no prompt in `scenarios/`
  today, which is evidence that it steals nothing and none that anybody needs
  it.
