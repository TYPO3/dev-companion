---
id: D-SKL-045
title: A build workflow names the guide at the step that needs it
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::theBrowserStepNamesTheGuidesThatAnswerIt
---

# D-SKL-045 — A build workflow names the guide at the step that needs it

**The step that asserts an editor's view in a browser names the testing guide
that answers it, by `documentId`.**

A session held both testing guides' ids from `typo3_project_describe`, reached
the step, gave up on a scripted backend login and shipped six backend previews
unverified. It called `typo3_rule_lookup` at no point in the session.

## Evidence

- `feedback/2026-08-17-205945`, a v14 demo site built as a sitepackage and a
  distribution extension on 14.3.6. Its fourth case is the one this entry is
  about. The `guides` array named both `any/testing/browser-check` and
  `project/testing/playwright`. The session wanted to verify its previews in a
  browser, and it stopped rather than opened either. Its own account of why is
  that it stopped, not that it was impossible.
- Both documents name that moment in their own front matter. `browser-check`
  says *when a screenshot or a browser session has to run against an
  installation that already has the content*. `playwright` says *when a
  repository that serves a TYPO3 site has no browser suite yet, for what a
  visitor gets and for what an editor does*. The session was in a site
  repository with no browser suite, with what an editor sees in hand.
- Read across `skills/` on 2026-08-18. Four skills name `typo3_rule_lookup`, all
  of them core: patch development, patch checkout, issue triage, patch review.
  `skills/base.md` step 1 names it as what fetches the `guides` ids. No
  build-side skill names it, and nothing under `skills/` names either guide.
- The route does not exist under another name either.
  `typo3-content-element-development` reaches the moment at *add browser
  coverage when JavaScript interaction, editor workflow or accessibility is part
  of the feature*, which names no lookup. Its closing handoff names
  `typo3-extension-testing` for test infrastructure. That skill carries its own
  `references/playwright.md` and names neither guide. So the surface hands on to
  a skill that has no route to the corpus either.
- `ProjectDescribe`'s `guides` field already records the cost from the other
  side: *four sessions in one week finished without learning they exist*. This
  is the fifth and the one that is different. The array was in its context and
  the session still did not fetch the ids, which is what the field description
  exists to fix.
- The two guides are not what the skill already ships.
  `skills/typo3-extension-testing/references/playwright.md` is method — choose a
  topology from evidence in the project, leave it unresolved where the checkout
  does not decide it. `knowledge/documents/project/testing/playwright.md` is the
  configuration, the backend login and a spec, whole. Which of the two a session
  needs is not a question the skill puts.

## Decided

- **Step 3, routing.** The corpus lacks nothing and no wording was wrong. The
  guides exist, the workflow reaches the moment they serve, and no file leads
  from the one to the other.
- **Queued, not closed on the spot.** The change is a line in a published skill,
  which [judging.rst](../../documentation/records/judging.rst) puts on the
  reviewed side. The card carries it at `normal`, because this is the same lever
  that failed after `D-ANS-061` and `D-SKL-030`, and not because one session
  asked.
- **The lever is the surface, not the base and not the answer.** This entry
  takes the feedback's second suggestion in the form
  [`D-SKL-030`](skl-030-a-review-surface-names-the-lookup-that-can-answer-it.md)
  already established. The moment is a step in a workflow, so the name goes
  there. `skills/base.md` is not touched, which is what
  [`D-SKL-001`](skl-001-the-order-a-task-starts-in-is-one-file.md) watches, and
  the `guides` array is where the ids already were.
- **Where the line goes is the work's first question, and it is a placement
  between two files.** The browser bullet in
  `typo3-content-element-development`, or the browser step in
  `typo3-extension-testing`, which is where the infrastructure lives and where
  the first skill hands over. `D-SKL-004` decided its own such placement on who
  asks, which is the question here too.
- **No requirement exists yet.** What would hold this is `R-SKL-022` one
  workflow over: a surface a tool can answer names that tool. Whether it
  generalises past a review is a question the change settles rather than one
  settled before it.
- **The feedback's first suggestion is not decided here.** A sentence in the
  build workflows that a symptom is a lookup trigger depends on the symptom that
  reaches anything. That is the axis question of `feedback/2026-08-17-212010`,
  in hand on its own branch. Measured here on 2026-08-18, it does not.
  `bin/cli hints:probe "the content elements render in reverse order"` returns
  `content-elements` and `content-element-shape` and not
  `datahandler-placement`. `"f:asset.css does not appear in the rendered page"`
  returns `css-source-build-boundaries` and `public-assets` and not
  `fluid-layouts-sections`. Both are the ids that would have answered. A
  sentence that sends a caller with a symptom to the index today sends it to a
  miss.

## Assumed

- That a session acts on a name at the surface where it did not act on the ids
  in the `guides` array. That is `D-ANS-061`'s open **Assumed**, restated by
  `D-SKL-030`. Nothing has yet measured a session that read a document because
  an answer named it.
- That the guide would have unblocked this session. It carries how to reach a
  DDEV site from a container and where the harness and its output go. The page
  module body with its asynchronous load, where the session stopped, is in
  neither half.
- That the browser step is worth a call on ordinary content-element work. The
  measure is one build where six previews shipped unverified.

## Wrong if

- A session reads the named lookup, fetches the guide and still ships the view
  unverified. Then the route was not the gap and the guide is what is short. The
  second **Assumed** above is where that would land.
- The workflow calls it on every element and the guide answers nothing on most
  of them. Then the bullet bought a call per element, and the route belongs to
  the work that produces an editor-facing view rather than to the surface.
- A build session reports that it read a guide off the `guides` array. Then the
  array was delivery after all, and what this entry moves bought nothing.

## Since then

The placement is the browser bullet of the content element skill, and who asks
decides it. The session that stopped had produced an editor-facing view and
wanted to see it, and a look is not a test layer. A handoff for test
infrastructure reaches the other file, so a session that has already decided to
build a suite reads a name behind it. Both ids stand at the one step with what
each alone answers, and the suite itself is still the testing skill's work.

The demand did generalise past a review, and not as the requirement that rests
on a checklist item with an answer in a report. A build step has no such
property, and the session passed this one.
