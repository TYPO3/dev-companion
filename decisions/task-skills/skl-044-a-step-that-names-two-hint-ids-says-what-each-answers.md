---
id: D-SKL-044
title: 'A step that names two hint ids says what each answers'
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::anInstallationIsBuiltInDependencyOrder
---

# D-SKL-044 — A step that names two hint ids says what each answers

**Step 5 of `typo3-development-installation` carries a clause per hint id and
closes on both answers. This entry rejects a second mention of
`project-build-and-scripts` later in the workflow.**

The step named two ids under one shared clause, and that clause described only
the first of them. A session fetched it, had the sentence's question answered in
full, and read the step as discharged.

## Evidence

- **The session.** `feedback/2026-08-17-211118`,
  `/home/benji/projects/site-demo` on 2026-08-17, a TYPO3 demo site built as a
  sitepackage plus a distribution extension. It called
  `id=project-configuration-files`, never called `id=project-build-and-scripts`,
  and reported the work finished. Five of the ten defects a reviewer then listed
  are in the hint it did not call. The sixth cost is the one it names as larger:
  the repository has no test suite at all.
- **The knowledge was there and is correct.**
  `bin/cli hints:probe "project build and scripts"` on 2026-08-18 returns
  `project-build-and-scripts` first. Read in `knowledge/hints/project.json` the
  same day, it carries every line the feedback quotes — the Playwright
  configuration and its specs, the static analysis configuration, the one-off
  seeding script and the keep-or-drop choice on it, that there is no
  `runTests.sh` and the scripts are the only answer to how anything is run, and
  naming them after what they do. So this is not step 1a, and the judgement
  needed no lookup about TYPO3.
- **The shared clause described the wrong half.** It read *says which of them
  belong to the project and which are generated*, which is
  `project-configuration-files`'s subject. That is `settings.php` against
  `additional.php`, and the environment that generates into both. The other
  hint's title is *Build/, the Scripts, and What Is Not Deployed* and it answers
  no part of that question. So a caller with the first answer in hand had
  positive evidence of a finished step. That is what the feedback reports of
  itself: "the first hint answered fully and well, so the step felt discharged."
- **The rest of the corpus already does it the other way.** Seven `id=` mentions
  across `skills/` on 2026-08-18, and six carry their own clause that says what
  that id alone answers. Step 4 of this same skill names three ids that way, and
  the two in `typo3-extension-testing/references/static-quality.md` are a step
  apart. Step 5 was the single exception, so this entry invents no form.

## Decided

- **Each id carries what it alone answers**, in the form step 4 of the same
  skill already uses. `typo3_hint_lookup` owns this, then an id and its clause.
- **The step closes on both answers**, since the ignore rules follow from both.
  So a caller with one in hand cannot read the step as satisfied.
- **The clause names the question, never the answer.** Where the tooling and the
  one-off scripts belong is what the id is for; `Build/` is a layout key the
  hint owns, and this skill's own preamble refuses to retain one.
- **Rejected: a second mention of `project-build-and-scripts` at the proof
  step.** What the session reports as the mechanism is the shared clause. Its
  observation that the hint describes work not yet started is its own
  speculation beside it. A second mention treats a symptom the corrected clause
  already addresses. It is a pattern nothing in `skills/` does anywhere, and
  every session that loads the file pays for it by the token. What would revive
  it is below rather than settled against.
- **Closed on the spot rather than queued.** The change is one sentence of body
  prose: no name, description, front matter, routing list, reference or
  `This skill owns` moves, no tool is added or dropped, and both ids were
  already named at this step. That is the ladder's step 4 — delivered and did
  not take — and its cheapest fix.

## Assumed

- That the shared clause is what decided the skip rather than the placement. The
  session states both and only the first is a property of the file; nothing
  measures which of the two a corrected step changes.
- That a caller reads a clause per id as two obligations. Step 4 has carried
  that form from its first day. Nobody has seen a session drop one of its three
  ids, which is an absence of sightings rather than a measurement.
- That the second id's clause is worth its tokens in every session that loads
  the skill. The count is in characters and not against what a session gives up
  for them.

## Wrong if

- A session with the corrected step in front of it fetches one id and stops.
  Then the wording was never what decided it, the answer is outside this file,
  and what is left to weigh is a gate at the end of the workflow — which is what
  `feedback/2026-08-17-212218` reports as a pattern across three steps rather
  than as this one.
- A session reads both ids at step 5 and still arrives at the proof step with no
  tooling and no suite. Then the placement is what has to move and the rejection
  above is what has to go. The answer landed at a moment the work it describes
  had not started.
- The crossing is what actually failed. This skill hands tests and static checks
  to `typo3-extension-testing`, that skill was never activated in the reported
  session, and the missing suite is on the far side of a boundary this step does
  not own. Then step 5's wording is a smaller finding sitting on top of one
  `R-SKL-003` already governs.

## Since then

A session weighed the gate the first **Wrong if** leaves to weigh on 2026-08-18.
The answer is that it waits for a fired form of this correction,
[`D-SKL-049`](skl-049-the-gate-at-the-end-of-a-workflow-waits-for-its-corrections.md).
So that **Wrong if** is now the trigger of another entry as well as of this one.
A session that fetches one id and stops with the corrected step in front of it
makes the gate's case. It does so on a file already fixed at the point of use.
