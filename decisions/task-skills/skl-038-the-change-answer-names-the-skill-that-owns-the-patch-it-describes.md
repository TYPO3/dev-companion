---
id: D-SKL-038
title: The change answer names the skill that owns the patch it describes
date: 2026-08-14
status: open
coveredBy:
  - ForgeTest::aPageOfTheBacklogIsHandedTheWorkflowThatOwnsIt
  - ForgeTest::theRecentEndCarriesNoTriageWorkflow
  - ForgeTest::theWorkflowStandsUnderThePageOfCandidates
  - GerritTest::aNamedChangeIsHandedTheWorkflowsThatOwnIt
---

# D-SKL-038 — The change answer names the skill that owns the patch it describes

**`typo3_gerrit_lookup` names the two core patch skills and the call the order
opens on, where a caller named one change.**

That answer is the one thing a review session which opened no skill did ask for,
and it hands back a ref and a remote and nothing about the workflow it has just
begun.

## Evidence

- **The session.**
  [`feedback/archive/2026-08-12-092545`](../../feedback/archive/2026-08-12-092545-a-german-language-review-request-activated-no.md),
  `/home/benji/projects/typo3-cms` on 2026-08-12, `claude-opus-5[1m]`. The brief
  was a German request naming Gerrit change 95169 by number and by review URL.
  No skill activated at any point, `typo3_project_describe`'s schema was loaded
  and the tool never called, `typo3_task_guide` and `typo3_server_scope` were
  never reached, and the client rendered no resource list. What it did call was
  `typo3_gerrit_lookup`, which handed it the ref and the remote it then fetched
  the patch set with by hand.
- **The second session of the shape, and one hypothesis fewer.**
  [`feedback/archive/2026-08-10-182404`](../../feedback/archive/2026-08-10-182404-a-review-request-quoting-the-skill-s-own.md)
  is the same request shape in the same checkout, judged as
  [`D-SKL-033`](skl-033-whether-a-skill-is-activated-is-the-clients-and-the-models.md).
  That report named two things that plausibly kept the skill shut: the language,
  and a request naming a local commit rather than a change on the review server.
  This one named the change, by number and by URL, and the skill stayed shut. So
  the second of the two is gone and the first is where it was.
- **What the answer carries today.** Read in this checkout on 2026-08-14:
  `GerritLookup::answer()` ends on two sentences, one holding the commit against
  `git rev-parse HEAD` and one saying the fetch goes to the review server rather
  than to `origin`. Both are about the checkout. No class below `src/Tool/`
  names a skill at all — `typo3_task_guide` is the only route into one, and it
  is data (`D-SKL-013`).
- **The route this server has names the wrong workflow for this brief.**
  `TaskGuide::answer()` run here on 2026-08-14 with the feedback's brief
  verbatim matches the `breaking` intent strongly and `patch-checkout` weakly,
  and names `typo3-extension-upgrade`. The same request in English — "review
  core patch 95169 and say whether it is breaking" — names
  `typo3-core-patch-development`. `typo3-core-patch-review` is carried by the
  `audit` intent alone, whose needles are "review the", "review this", "review
  of" and "reviewing", and neither brief contains one of them.
- **The same shape one level down is already decided.**
  [`D-ANS-061`](../answers/ans-061-an-answer-that-names-a-document-hands-it-over.md):
  a `uri` in an answer is not delivery, and the lever is the tool the session
  does call rather than the one it should have called.
  `TestRunGuide::SCRIPTS_GUIDE` and `BROWSER_CHECK_GUIDE` are what came of it,
  both placed at the moment the caller is certainly reading.
- **The corpus reaches nothing on that brief.** `bin/cli hints:probe` with the
  request verbatim matches no hint and returns the index, detecting the domain
  `php`. Everything below `knowledge/` is English and this server says so three
  times, so that is a property of the matcher rather than a gap this session
  found.

## Decided

- The `change` form of `typo3_gerrit_lookup` names `typo3-core-patch-review` and
  `typo3-core-patch-checkout`, and `typo3_project_describe` as the call the
  order opens on. A caller holding one change is about to review it or to fetch
  it, and those two workflows own it.
- The `issue` form takes none of it. "Has somebody already fixed this" precedes
  triage, patch development and review alike, and `D-SKL-013` already declines
  to route the `submission` intent for spanning two skills. The same holds for
  `typo3_forge_lookup`, which is that question one host over.
- Not `typo3_server_scope`. Two sessions finished a task without calling it for
  the same stated reason, and naming a tool nobody invokes is what `D-ANS-061`
  ruled out. What the tail names is a workflow and a call, and both are acts.
- Against a sweep of the other tools' answers. One tool and one moment:
  `D-SKL-013`'s third **Wrong if** is what a route invented for a row nobody
  asked for costs.
- The descriptions stay as they are. `D-SKL-033` weighed the wording and the
  German trigger words, and this feedback adds a session to its evidence rather
  than reopening it.
- **The route is repaired too, and separately.** A review request naming a
  change number reaching an upgrade or a development workflow is a defect in
  `knowledge/task-intents.json` rather than in this answer, and which repair it
  takes — widening `audit`, or `breaking` not routing where the brief is a
  review — is a reading of the matcher that a card of its own carries.

## Assumed

- That a session given a skill's name in an answer loads it. Nothing here
  measures that; what is measured is sessions not acting on a bare uri
  (`D-ANS-061`), and `D-SKL-013` carries the same assumption for the guide.
- That the moment is what a description cannot have. The tail arrives after the
  session has committed to the task and asked a question of its own, rather than
  competing for the brief, and nothing here can see whether that is read
  differently.
- That naming a skill a project has not installed costs nothing, which is
  `D-SKL-013`'s second **Assumed** unchanged.

## Wrong if

- A session reports reading the tail and reviewing by hand anyway. Then the name
  is one step short of the handover here too, and what is left is the order
  itself in the answer — the shape `TestRunGuide::SCRIPTS_GUIDE` took.
- A session that already had the skill open reports the tail as noise on an
  answer it asked a narrow question of. Then it belongs behind a condition the
  way the fetch sentence is, rather than on every change answer.
- A review session reports no skill and no entry point with this tail in place.
  Then the answering side is not the channel either, and what is left untried is
  the project's own agent instruction file, which `D-SKL-033` recorded.

## Since then

The backlog answer is a second moment, and the bullet that declined the tail was
written for the issue form: the `open` form takes it and the `issue` form keeps
none of it. The tail is the neglected end's, and the enumeration gained another.

The third **Wrong if** is met. Three sessions on 2026-09-09 read a `change`
answer carrying this tail, which has stood in it since 2026-08-24, and none of
them opened a skill.
[`feedback/2026-09-09-182500`](../../feedback/2026-09-09-182500-three-matching-typo3-core-patch-skills-stayed.md)
counts its own: two calls to `typo3_gerrit_lookup`, one to
`typo3_project_describe`, none to `typo3_task_guide`, no activation, across a
review of a 137-file patch and five amends of its commit message. So the
answering side is not the channel either, and what is left is what this entry
named: the project's own agent instruction file, which `D-SKL-033` carries.
