---
id: D-GUI-012
title: The brief names the guide the recognized work belongs to
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::aBriefNamesTheGuideTheWorkIsWrittenUpIn
  - KnowledgeTest::everyGuideAnIntentNamesIsADocument
  - ProjectTest::theCallEveryTaskOpensWithNamesTheGuidesThereAre
---

# D-GUI-012 — The brief names the guide the recognized work belongs to

**`typo3_task_guide` names the knowledge document the work it recognized belongs
to, beside the skill it already names.** The brief searches three
core-contribution documents, so the answer that knows what the task is hands a
session outside the core no guide at all.

## Evidence

- `feedback/2026-08-18-074226`. The session learned the guides exist from one
  place, the `guides` key of `typo3_project_describe`, while it read that answer
  for the version and the sites. It made no `typo3_rule_lookup` call in the
  whole session. It then verified a render change in a browser without
  `any/testing/browser-check` and added functional tests without
  `extension/testing/phpunit`.
- Measured in this worktree on 2026-08-18. `typo3_task_guide` for "add unit and
  functional tests for a ViewHelper in the blog extension", with an extension
  path, names the skill `typo3-extension-testing`. It names one document,
  `core/contribution/rules`. For "prove a rendering change in the browser after
  fixing a frontend crash" it names no document at all.
- `TaskIntents::RULE_DOCUMENTS` is why. A brief searches
  `core/contribution/rules`, `core/contribution/commit-messages` and
  `core/contribution/gerrit-workflow`, so every document under `any/`,
  `extension/` and `project/` is unreachable from the tool that recognized the
  task.
- The delivery already stands in the same answer. `Prose::sections()` names each
  page the excerpts came from and the `typo3_rule_lookup` call with `documentId`
  that reads it whole, which is `D-ANS-070`. A named document arrives as a call
  rather than as an address, which is `D-ANS-061`.
- Two more feedback out of the same checkout report the shape from the skill
  side: `feedback/2026-08-18-074245` and `feedback/2026-08-18-081159`. Each is a
  pointer offered once, at orientation, and needed at a moment the session
  reached hours later.

## Decided

- **The ladder's step 2, delivery.** The corpus is here, the client rendered no
  resource list. The only answer that names a guide is the call a session makes
  before it knows what the work is. The gap is placement rather than a document.
- **The placement is `typo3_task_guide`.** It is the one answer that has already
  recognized what the work is, and it names the skill that owns it. The guide
  belongs on the same line.
- **Queued rather than made on the spot.** It moves
  `TaskIntents::RULE_DOCUMENTS` and the brief's `rules`, which is a declared
  output schema. Both are what `documentation/records/judging.rst` keeps off the
  spot.
- **The guide arrives as the call that reads it**, the way the matched sections
  in the same answer already do. Not as a `typo3://guides` address, and not as a
  page inline in a brief that already carries hints, rules, checks and a
  checklist.
- **The name is the guide of the recognized work**, not every document whose
  words match the task text. A brief that searches the whole corpus is
  `typo3_rule_lookup` run a second time, and the intent is what tells the two
  apart.
- **Which of two shapes carries it is the todo's first step.** Wider searched
  documents per scope, or a guide named on the intent beside `skill` and
  `skillCore`. Both stand against what each intent owns today rather than as a
  choice here.

## Assumed

- A session reads a guide named mid-task where it did not read the same list at
  orientation. Nothing here measures that, and the session that reported says it
  cannot tell whether either guide would have changed what it did.
- The work a brief recognizes is the work the session is in. That holds for a
  call at the moment the task changed shape and not for the one at the start.
  That is the half `feedback/2026-08-18-081159` reports.

## Wrong if

- A brief names a guide that does not fit the work, a test recognized from a
  word rather than from the task. That would say the name is a search and
  belongs in `typo3_rule_lookup`.
- A session reports that the guide arrived in the brief and it still did not
  read it. That would say the placement is not the lever and the page has to
  arrive whole.
- A named guide repeats what the skill on the same line carries, which would say
  the two are one pointer rather than two.

## Since then

The placement landed on the intent as a `guide` field of its own on 2026-08-18,
and six intents name one now. The two compatibility pages went in after an audit
reached none of the five, and no intent recognized compatibility work at all
until then. A brief that changes nothing withholds a guide by change type.
`guideCore` stays empty for a reason one core page outlived: a core intent names
its page in `tools` instead.

What the reports bound is what a name can buy. Three sessions report a page
named to them and unread. A fourth reports a rule that arrived whole in the
answer, got an approved quote, and still no action. So the reference gained what
a name cannot carry. The line that says when to read it, which is the page's own
declaration, and the tool that takes its id.
