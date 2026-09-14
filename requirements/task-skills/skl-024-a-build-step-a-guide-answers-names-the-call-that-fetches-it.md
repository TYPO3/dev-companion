---
id: R-SKL-024
title: 'A build step a guide answers names the call that fetches it'
status: held
restsOn: [D-SKL-045]
heldBy:
  - SkillTest::theBrowserStepNamesTheGuidesThatAnswerIt
---

# R-SKL-024 — A build step a guide answers names the call that fetches it

**A workflow step a whole procedure answers names `typo3_rule_lookup` with that
`documentId`, at the step.**

A session passes a build step rather than disposes of it, so nothing brings the
session back to it. What the step does not name is what the session settles for
itself, out of whatever it arrived with. The ids in `typo3_project_describe`'s
`guides` array arrive at the start of a session, hundreds of calls before the
one that matters becomes relevant. To hold them is not the same as to receive a
route to one.
[`R-SKL-022`](skl-022-a-review-surface-names-the-lookup-that-can-answer-it.md)
is the same demand one workflow over, on a surface a report disposes of.

## From

A TYPO3 v14 demo site built on 14.3.6 as a sitepackage plus a distribution
extension (`feedback/2026-08-17-205945`, 2026-08-17). It held both test guide
ids from `typo3_project_describe`. It reached the step where six backend
previews had to appear in a browser, gave up on a scripted backend login and
shipped them unverified. It called `typo3_rule_lookup` at no point in the
session, and its own account is that it stopped rather than that it could not.
