---
id: D-EVI-002
title: A skill crossing is read rather than run
date: 2026-07-31
status: confirmed
---

# D-EVI-002 — A skill crossing is read rather than run

**The skill text and the contract case read by hand hold that a session hands
over from one task skill to another.**

No forward run will exist to produce it.

Every other half of `R-SKL-003` has evidence. This one asked for a run that
cannot exist, and the feedback that asked for it stayed open for that reason
alone.

## Evidence

- The fourth `REVIEW-01` run shows a session that routes its findings to the
  `typo3-extension-documentation`, `typo3-content-element-development` and
  `typo3-extension-testing` workflows by name. It keeps conformance responsible
  for a second check of them. So the name of the next owner has evidence. What
  no run shows is the activation itself. A review stops at findings by design,
  so `REVIEW-01` cannot produce it however often it runs. Re-read on 2026-07-31,
  the skill still carries the transition in the order the test asserts.
  Implementation verified, workflow stopped, documentation skill activated,
  extension scope carried across.

## Decided

- `SkillTest::backendModuleDocumentationIsAnExplicitSkillTransition` and
  `SKILL-07` read by hand still hold the crossing, and `R-SKL-003` says so in as
  many words. Rejected: a fourth forward scenario in the shape of an
  implementation task. `D-EVI-001` admits only an open review as forward
  evidence. Nobody can rely on a prompt broad enough to qualify to reach this
  boundary at all. One that could would name the route, which is the thing that
  decision stands against.

## Assumed

- That the words decide the behavior. An assertion on the order of four
  sentences in `SKILL.md` stands in for what an agent does with them. That is a
  proxy, and it is the only one available.

## Wrong if

- A forward run that happens to cross the boundary shows the session edit
  documentation with the backend-module skill still the only active one. The
  words would then be present and inert, and the proxy worth nothing.
  `R-SKL-003` would be back in need of evidence this decision says it will not
  get.

## Confirmed on 2026-08-02

The **Wrong if** has not happened and nothing that ran could have produced it.
Of the sessions in the four checkouts since the words landed, not one edited a
file. The single activation of the skill predates the words by 75 minutes. What
that settles is that the read is cheap where there is something to read. The
failing shape is a `Skill` call and an `Edit` path with nothing between them.
What is absent is a run. A review neither edits nor reaches this skill, so what
would produce it is the contract case `SKILL-07`.
