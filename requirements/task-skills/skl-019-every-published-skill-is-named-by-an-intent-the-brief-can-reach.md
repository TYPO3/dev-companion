---
id: R-SKL-019
title: 'Every published skill is named by an intent the brief can reach'
status: held
restsOn: [D-SKL-023]
heldBy:
  - SkillTest::aCoreTriageReachesTheSkillThatOwnsItWithoutNamingAPath
  - SkillTest::everyPublishedSkillIsNamedByAnIntent
---

# R-SKL-019 — Every published skill is named by an intent the brief can reach

**At least one entry in `knowledge/task-intents.json` names every skill this
repository publishes.**

A client selects a skill on its description; `typo3_task_guide` selects one on
the intents. Only a caller who already knew a skill existed reaches one that is
in the first and not the second. The guide answers such a task with the nearest
intent that did match, which is a different workflow, confidently named.

## From

`feedback/2026-08-07-233443`, 2026-08-07. A core triage described as "Triage an
old open core bug report" got `skills: ["typo3-extension-conformance"]` with a
patch-review checklist. The checkout reported as `core-checkout` with no
project-own extensions. `typo3-core-issue-triage` owns that task and no intent
names it; nor do any name `typo3-core-patch-checkout` and
`typo3-extension-documentation`. Nine of twelve were reachable.

**Built on 2026-08-08.** `triage` and `patch-checkout` are intents of their own,
core-scoped the way `submission` is. `changelog` names
`typo3-extension-documentation` outside the core and
`typo3-core-patch-development` inside it. Twelve of twelve are reachable.

Reachable took a second half. "Triage an old open core bug report" carries none
of the markers `Scope::isCoreWork` reads. So a core-scoped intent fell to weak
on the very call that found the hole. Every intent that did match answered with
its extension side. The work that ends before a patch names the core as a
tracker and a checkout. `core issue`, `core bug`, `core checkout`,
`core backlog` and `core tracker` are markers now.

## Held by

The first has one exemption and it is in the code rather than in a list. A draft
is not a published skill, and a draft that routes can reach is one nobody chose.
