---
id: R-GUI-013
title: 'A brief names the guide the recognized work is written up in'
status: held
restsOn: [D-GUI-012]
heldBy:
  - HintsTest::aBriefNamesTheGuideTheWorkIsWrittenUpIn
  - KnowledgeTest::aGuideNamedOutsideTheCoreIsNotTheCoresOwn
  - KnowledgeTest::everyGuideAnIntentNamesIsADocument
---

# R-GUI-013 — A brief names the guide the recognized work is written up in

**Where a knowledge document writes up the work `typo3_task_guide` recognized,
the brief names it, as the `typo3_rule_lookup` call with `documentId` that reads
it.**

Otherwise the corpus appears once per session, in the `guides` key of
`typo3_project_describe`. That is the call a session makes before it knows what
the work is. A session whose client renders no resource list has no second way
in. So a page that arrives before the work arrives is one nobody goes back for.
The pointer is a name and not the page. A brief is one call inside a procedure,
and the procedure inline in it would replace the read it stands for.

## From

`feedback/2026-08-18-074226`. The session read the guides list while it
diagnosed a 404, and turned to test work three user turns later. It finished
with functional tests added without `extension/testing/phpunit` and a render
verified in a browser without `any/testing/browser-check`. Measured in this
worktree the same day, the brief for that test work named
`core/contribution/rules`. That is the page that judges a core patch, handed to
somebody's package.
