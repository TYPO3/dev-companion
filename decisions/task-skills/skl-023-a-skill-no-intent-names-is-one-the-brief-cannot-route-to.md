---
id: D-SKL-023
title: 'A skill no intent names is one the brief cannot route to'
date: 2026-08-08
status: open
coveredBy:
  - SkillTest::everyPublishedSkillIsNamedByAnIntent
---

# D-SKL-023 — A skill no intent names is one the brief cannot route to

**No entry in `knowledge/task-intents.json` names three of the twelve published
skills. So `typo3_task_guide` cannot route to them and routes somewhere else
instead.**

## Evidence

- `feedback/2026-08-07-233443`. A session on a core bug triage called the guide
  as `references/base.md` step 3 requires. It described the task as "Triage an
  old open core bug report". It got `skills: ["typo3-extension-conformance"]`, a
  workflow to review an extension or sitepackage repository. That came in a
  checkout `typo3_project_describe` had reported one call earlier as
  `core-checkout` with `extensions: []`.
- Re-run on 2026-08-08 with the session's own arguments: same answer. The
  checklist that comes with it is patch-review content. That is "enumerate what
  it removes or renames before judging it", extension-scanner matchers, `[!!!]`
  prefixes, `checkRst` over a core diff. A triage writes no diff. The session
  used none of it.
- The cause is not a bad choice by the `audit` intent. No intent at all names
  `typo3-core-issue-triage`, and no intent's `match` or `matchWeak` carries the
  word "triage". The same holds for `typo3-core-patch-checkout` and
  `typo3-extension-documentation`: nine of the twelve published skills are
  reachable from the guide and three are not.
- So the guide answered with the nearest intent that did match, which for
  read-only work is `audit`. Its `skill` is the extension conformance workflow
  and its `skillCore` is the patch review one. Neither owns a triage.
- The session says the call still paid for itself on its suite and option
  blocks. A session that trusts the routing field over the skill list "would
  have run an extension conformance review inside a core checkout".

## Decided

- A skill this repository publishes and the brief cannot name is a routing hole
  rather than a preference. The client selects on descriptions and the guide
  selects on intents. A skill present in the first and absent from the second is
  in reach only for a caller who already knew it existed.
- So a check holds the set rather than memory. At least one intent names every
  published skill. A new skill that arrives without one fails a check, before a
  session that got the wrong workflow discovers it.
- What each of the three needs is not decided here. Triage is a task shape with
  its own vocabulary: tracker, Forge number, backlog, "is this still a thing".
  It is a candidate for an intent of its own. The other two may belong on
  entries that exist. That is the todo's read.
- The checklist is a second finding on the same call and is not the same fix.
  `audit` returns removal, extension-scanner and changelog-file items to a task
  that produces no diff, which the domain withhold rule already does for hints.

## Assumed

- The three are the whole of it. Measured today against `skills/` and the intent
  file. A skill published under another mechanism would not count.
- Routing to the wrong skill costs more than routing to none. The session did
  not follow it, so the measure is the wrong answer rather than its consequence.

## Wrong if

- A skill turns out to be unroutable on purpose, in reach only from its own
  description because the guide cannot tell the task apart. That would make this
  a check with an exemption rather than an invariant.
- A session reports that triage's own intent pulls patch work into it, which
  would say the vocabulary does not separate the two.

## Since then

The intent names the skill, and the word people call the tracker by reaches it
weakly. The scope gate matches with `str_contains`, and the needle sits in the
triage intent's weak list. So both gates read the word as the tracker's name and
the repair is there. Against the feedback's own suggestion, which is to move the
triage clause to the other skill.
