---
id: D-GUI-005
title: 'The product premise is one statement on every brief'
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::everyBriefOpensOnThePremiseADefectIsJudgedBy
---

# D-GUI-005 — The product premise is one statement on every brief

**What a CMS decides a defect by stands once, as the first item of the
`typo3_task_guide` checklist. The mechanisms it explains stay where they already
are.**

Three cards were about to say it three times, cache busting, cache invalidation,
the rendered preview. None of the three is the reason a session got the
assessment wrong.

## Evidence

- The feedback. `feedback/2026-08-02-145043`, `claude-opus-5[1m]`, from
  `/home/benji/projects/typo3-cms`, `tool: typo3_task_guide`: "Every fact I
  needed was in the catalogue or the checkout. What was missing was the premise
  that decides which facts matter." Its own suggestion names this tool's bugfix
  checklist. It names the wording "assess the report by the outcome for editor
  and visitor before assessing the code that was written".
- The corpus does not state it. `bin/cli hints:probe` on 2026-08-03 with
  "content changes and what is delivered has to be the current version" reaches
  `form-framework` on 52 points of text alone. With "judge a bug report by the
  outcome for the editor and the visitor" it reaches nothing at all, 76 hints
  back as the index.
- The mechanisms it explains have a place or a card, each once. Cache busting is
  `fluid-resource-uris` in `knowledge/hints/fluid.json`, which states that the
  publisher applies it and that every resource URI carries one since 14. Cache
  invalidation is `todo/open/2026-08-02-211403` on
  [`D-KNW-027`](../knowledge/knw-027-which-caches-a-change-invalidates-is-a-subject-this-server-owns.md).
  The rendered preview is `todo/open/2026-08-02-200948` on
  [`D-KNW-017`](../knowledge/knw-017-a-verification-question-is-routed-to-the-layer-that-verifies-it.md).
- The checklist had nothing of this kind. Five items: confirm the branch,
  inspect nearby code, keep the patch focused, cover it, run the checks. The
  `bugfix` block adds a reproduction and a check of older branches. All of them
  are about the patch, and none says what the change is for.
- The brief is what a build or fix task passes through. `skills/base.md` orders
  it as step 3 of every task, and the session that reported called it. It
  composes per call rather than costs every caller in every project.
- One sentence the feedback offers is not true of the code. "Nobody offers an
  option to omit the processing checksum" is its argument against an option.
  `useCacheBusting` is an argument of `Uri\ResourceViewHelper` on
  `.checkouts/14.3` and `.checkouts/main`, line 62, with true as the default.
  `fluid-resource-uris` states the same. So the premise stands with no claim
  about what may be off.

## Decided

- One statement rather than three, and it names an outcome rather than a
  mechanism. Cache busting, cache invalidation and a rendered preview as a check
  all follow from it, and each has a place of its own already.
- First in the checklist, before "confirm the branch". It is what decides which
  of the items under it matter. A reader meets an item somewhere in the middle
  after the assessment it should have changed.
- On the base checklist rather than in the `bugfix` block the feedback asked
  for. A feature delivers the old version just as a bug does, and a statement
  per change type is the same sentence in two places.
- Not in `skills/base.md` and not in the `instructions`.
  [`D-FBK-024`](../feedback/fbk-024-a-feedback-about-the-callers-conduct-toward-its-user-names-no-surface.md)
  prices both. 2048 characters on one client's evidence
  ([`D-ANS-004`](../answers/ans-004-the-instruction-budget-is-2048-characters-on-one-clients-evidence.md)),
  and a base skill whose length
  [`D-SKL-001`](../task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md)
  watches. Neither cost is due for a statement the tool at step 3 can carry.
- Two of the feedback's three concrete clauses stay out, and the feedback goes
  to the archive with that said rather than trimmed to them. That an editor does
  not tell a FAL image from a package resource is what `fluid-resource-uris`
  already states as code. `PublicResourceInterface` hands both to one
  generation. That two APIs for one outcome make their difference the bug is a
  claim about API design rather than about TYPO3. It is the kind of second
  statement this entry exists to refuse.
- It stands as a statement about TYPO3 and not as a rule about how a session
  conducts itself, which is the boundary `D-FBK-024` draws. The claim is what
  the product owes an editor and a visitor. When to stop and ask the user is the
  question that entry leaves open.

## Assumed

- That the premise carries the three mechanisms rather than needs them beside
  it. The session that reported had the facts and drew the wrong conclusion,
  which is what says the gap was the premise. Nothing has measured a session
  that has the premise and no mechanism.
- That a caller reads the checklist. The session that reported called this tool
  and recorded what it got, but no transcript shows which part of the answer it
  acted on.
- That the premise reads as true outside the core. It is the same product for a
  sitepackage, and the sentence names no core artifact, so the outside-core
  filter keeps it. But every feedback behind it came from core work or from one
  site project.

## Wrong if

- A session reads the premise and still assesses a report by whether the code
  used the API correctly. The words were then not the gap, and what remains to
  suspect is the surface. A reader meets a checklist item after the assessment
  has formed.
- `typo3_task_guide` gains the shape
  [`R-GUI-006`](../../requirements/guides/gui-006-a-review-is-not-answered-with-a-checklist-for-changing-something.md)
  demands and the premise falls out with the change checklist. It holds for a
  review more than for a patch, so it would need a place that survives that
  split.
- A session agrees with the premise and ships a stale asset anyway. Then the
  outcome stands where the mechanism was the need, and the three cards are the
  answer rather than its corollaries.
