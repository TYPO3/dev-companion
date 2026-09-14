---
id: D-KNW-005
title: '`Scope` is the one word for which work a statement is for'
date: 2026-08-02
status: confirmed
coveredBy:
  - KnowledgeTest::everyScopeInTheCorpusIsOneTheEnumDeclares
---

# D-KNW-005 — `Scope` is the one word for which work a statement is for

**`binding`, `provenance`, `audience` and the `outsideCore` boolean are one
backed enum, `Knowledge\Scope`, with the cases `core`, `project`, `extension`,
`any` and `uncertain`.**

Four fields asked which kind of work an answer is for and nobody could compare
one with another. `D-KNW-003` kept two of them apart deliberately and named what
would show that wrong: a value that reads naturally on both axes. Naming the
three audiences of `R-AUD-001` outright is that value.

## Evidence

- The four, as they stood. `binding` on 28 hint and intent entries, one value,
  `core`. `provenance` on the 16 covered topics, three values: four `core-only`,
  eight `transferable`, four `installation`. `audience` in five tool output
  schemas, three values, one of them spelled as a negation — `outside-core`.
  `outsideCore`, a required boolean beside it in the same five schemas, which
  says the same thing again with less room.
- `R-AUD-001` has named three audiences since 2026-07-29 and the code
  implemented two of them. An extension author and a site developer both read
  `outside-core`, so no answer could serve either. The corpus had already worked
  around it in prose. `project-repository-layout` and
  `extension-repository-layout` are two hints, `core-tests` and
  `project-extension-tests` are two more. What separates each pair is a sentence
  rather than a field.

## Decided

- The word is `scope`, not `audience`. `audience` stays the idea the repository
  stands on, `requirements/audience/` and R-AUD-001 through R-AUD-006. `scope`
  is what the code and the payloads say, because one word beats a second one
  that means the same thing.
- An enum rather than string constants, so nothing can write, pass or return a
  value that is not one of the five. `Scope::from()` on the corpus is what turns
  a typo in a JSON file into a failure at load.
- `installation` is not a scope and is gone as a value. It said where an answer
  comes from, which is what `source` on the same entry already says. A slot for
  it was what kept an installation-backed topic from a statement of who it is
  for. The four topics that had it are now `any`, and their `source` names the
  installation.
- `outsideCore` goes from the five output schemas rather than stays beside the
  enum. It is `scope !== core`, every tool that carried it already carried the
  scope, and `D-SCO-006` predicted the rename in 2026-07-29. This is a breaking
  change to those schemas.
- `Knowledge\Scope` was the class that held the server-scope map, and that moved
  to `Knowledge\Coverage`. The name goes to the vocabulary because the
  vocabulary is what the rest of the server says. One tool and one resource
  index read the map.
- `any` and `uncertain` belong to one side each rather than to both. A statement
  can hold wherever anybody writes TYPO3 and a path cannot, because a path is
  one piece of work. A path can be one nothing placed, and a statement nobody
  could place is one nobody should have written. `Scope::ofPaths()` and
  `Scope::ofKnowledge()` are the two sets.

## Assumed

- `project` and `extension` can be told apart from structure. `PROJECT_WORK` is
  the evidence, `config/sites/`, `config/system/`, `public/`, `var/`, `.ddev/`,
  read after the extension containers. So a sitepackage's own `Configuration/`
  does not pass for the site's. Where nothing structural says which, a Composer
  project falls to `project`, which is the repository the session is in rather
  than a package inside it.
- Answering `project` where the old code answered `outside-core` costs nothing,
  because both are outside the core and R-SCO-002 draws its line there. Eleven
  of the paths in `theSysextSignalAloneAnsweredEveryDecisionTheRecordedRunsMade`
  now answer `project` rather than `extension`. That test narrowed to the claim
  the recorded runs actually made.

## Wrong if

- A caller gets extension advice for site-configuration work or the reverse.
  `config/sites/` answered as a package, or a path under `packages/` answered as
  the project around it. The two are one repository in most sessions, which is
  what made one value survive this long. It is what will make a wrong split hard
  to notice.
- A fourth kind of work arrives that is none of the five — a distribution, or a
  TYPO3 fork maintained downstream. Then the enum is the thing that has to
  change rather than a string somewhere. That is the cost this entry accepted
  for the guarantee above.

## Confirmed on 2026-08-22

Five cases, and the case the second **Wrong if** names arrived and asked for no
sixth. A session built a distribution extension with a site's content, which the
enum answers as `extension` because that is what it is by structure.

Nothing reports the first in the shape it names. What did arrive is the other
failure, `uncertain` for a standalone extension repository, with core checks
behind it. That is the signals that fail to place the work rather than the
vocabulary that puts it in the wrong case. The answer said so where it could not
tell.
