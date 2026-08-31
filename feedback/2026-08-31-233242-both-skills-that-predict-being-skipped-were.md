---
date: 2026-08-31T23:32:42+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-extension-testing, typo3-extension-documentation, typo3-content-element-development, typo3-development-installation
directory: /home/benji/projects/site-tierheim
---

# Both skills that predict being skipped were skipped again, in the same session, by me

## Observation

Task: relaunch a German animal shelter website on TYPO3 v14 with a new frontend and the legacy content migrated; the visible part of my transcript covers renaming the extensions, setting up a test harness, removing the finished migration package and rewriting the README. My context begins at a summary of earlier turns, so I answer for the part I can see.

In the visible window I activated ZERO skills. Two were reported to me as activated before compaction: typo3-content-element-development and typo3-development-installation. Their reference files (checklist.md, base.md) were in my context; I did not act on the order base.md prescribes.

The sharp finding is not that I skipped a skill. It is WHICH ones, and that both name this exact failure in their own text.

typo3-content-element-development says, verbatim: "**Where the layer this element needs has no harness, invoke `typo3-extension-testing` before writing the test.** That is a step, not a note about ownership... It is written as a step because naming that skill at the end of this file did not fire as one: a session followed this workflow to the commit on six elements and wrote no test at any layer."

In this session I: required typo3/testing-framework, copied UnitTests.xml and FunctionalTests.xml, adjusted their testsuite paths and bootstrap, added web_environment DB credentials to .ddev/config.yaml, wrote a 13-test functional test class for a DataProcessor with a CSV fixture, later deleted the unit suite as empty. I never activated typo3-extension-testing. The escalation from note to step did not fire either.

Same file: "**When the element is verified and something has to describe it, invoke `typo3-extension-documentation` before editing a manual or a README.** ... The same session wrote three README files by hand." I rewrote a 426-line README end to end by hand. Never activated it.

What actually reached me instead was typo3_hint_lookup returning the id project-extension-tests, whose documents array pointed at typo3://guides/extension/testing/phpunit. That guide carried the procedure end to end and is why the harness is correct. So the knowledge arrived — through a tool call I happened to make, not through the routing that was built for it.

The moment one would have had to fire: the user wrote "dafür gibt es auch eigene test packages" (there are dedicated test packages for that) and I was looking at packages/*/Tests/ that did not exist yet. Later: "die readme ist auch noch auf deutsch" with README.md open. Both are unambiguous triggers in the skills' own words.

Why I think it did not fire: a skill is chosen on its description, and in a session already deep in implementation nothing re-reads descriptions. The cross-references live in the body of an already-loaded skill, which is read once at activation and then competes with 90 shell calls.

## Query

Session task: "wir wollen http://www.tierheim-wiesbaden.de/ relaunchen auf TYPO3 basis, wir brauchen eine typo3 v14 mit neuem frontend". Trigger sentences that should have activated a skill and did not: "dafür gibt es auch eigene test packages" (typo3-extension-testing), "die readme ist auch noch auf deutsch" (typo3-extension-documentation). Files open at those moments: Build/UnitTests.xml, Build/FunctionalTests.xml, packages/animalshelter_animals/Tests/Functional/, README.md.

## Suggestion

Two things would have changed the outcome.

First: make the cross-reference a runtime nudge rather than body text. When typo3_hint_lookup returns project-extension-tests, or typo3_rule_lookup returns extension/testing/phpunit, the answer could carry a line naming the skill that owns the workflow the caller is evidently in ("this is typo3-extension-testing's; load it before writing the test"). A caller who is already calling tools will read a tool answer; they will not re-read a skill body.

Second: the skills know they are being skipped — both say so with a past example. That self-knowledge could be turned into a check the caller cannot pass over. The strongest form: have typo3_task_guide, when its task text mentions tests or documentation, refuse to answer the narrow question and name the owning skill first.

Concretely for this session, one line in the phpunit guide's opening — "if you are setting a suite up rather than repairing a configuration, invoke typo3-extension-testing first; this page is one step of it" — would have caught me, because I read that guide whole.
