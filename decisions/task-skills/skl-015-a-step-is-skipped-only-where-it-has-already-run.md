---
id: D-SKL-015
title: 'A step is skipped only where it has already run'
date: 2026-08-04
status: revoked
revokedBy: D-SKL-034
coveredBy: []
---

# D-SKL-015 — A step is skipped only where it has already run

**A session skips step 3 of `skills/base.md` where the guide named this skill,
and step 5 where the change touches no TYPO3 API.**

The base prescribed both steps and one session skipped them, and it reported
that rather than left it in the transcript. What to write was a question about
what the maintainer wants, and the maintainer answered it on 2026-08-04.

## Evidence

- **The session.** `feedback/2026-08-04-055741` and
  `feedback/2026-08-04-055715`, `/home/benji/projects/ext-guidedtour` on
  2026-08-04, a German request for a php-cs-fixer setup in a TYPO3 14.3
  extension with no test or static-quality infrastructure. It activated
  `typo3-extension-testing`, read `references/static-quality.md`, and skipped
  steps 3 and 5 of the base that skill carries. Its own warning is why this was
  asked at all: "a prescription that gets skipped teaches the next reader to
  skip the ones that matter too."
- **Two ways into a skill, and only one of them has already made the call.**
  `D-SKL-013` gave `typo3_task_guide` the name of the task skill that owns the
  work. So a session can arrive here from the guide's own answer or from the
  skill's `description`. `055715` reports the second. The schema loaded in the
  same batch as the scope tools and the session never made the call. The
  reference already carried the layer's workflow.
- **What the guide carries that no skill can.** Read in `src/Tool/TaskGuide.php`
  on 2026-08-04. The brief comes from `paths` as well as `task`, since the
  scopes, the groups and `Hints::find()` all run over the paths. Its own
  description calls it "a task checklist enriched with matching hints and
  relevant core checks". A skill is a published file in another project and
  knows none of the caller's paths.
- **And the step that would go with it today.** `TaskGuide` appends
  `typo3_commit_message_guide` to the checklist and to the next lookups for
  every task that changes a file, with `workflow="project"` outside the core.
  `D-SKL-014` decided on the same day that the skills whose workflow ends in a
  change name it too. It waits in the queue rather than stands in their bodies.
  A grep over `skills/` on 2026-08-04 finds the tool in
  `typo3-core-patch-development` and `typo3-core-patch-review` and in none of
  the ones for extensions.
- **What the sweep costs where it can find nothing.** Step 5 is one
  `typo3_changelog_lookup` per declared major per tag, which is the most
  expensive step of the order. `055715` states the condition it skipped under:
  "the change was tooling and whitespace, touching no TYPO3 API".

## Decided

- **Step 3's condition is the route, not the coverage.** The guide's own answer
  named the skill, so the call already happened and the base asks for it a
  second time. That is the redundancy the feedback hit.
- **The broad reading is rejected: "skip it whenever a skill covers the task end
  to end".** It costs the path-specific half of the brief, which no skill
  carries, and it costs the commit step until `D-SKL-014` is in the skill
  bodies. A session that matched the skill on its `description` has had neither.
- **Step 5's condition is that a deprecation is a statement about API the
  package calls.** A change that calls none leaves the sweep empty before it
  runs, so the condition is emptiness rather than unlikelihood.
- **The two are conditions on different steps and each stands at its own step.**
  Read together they become one licence to shorten the order, which is what
  `055741` asked for and what it does not get.
- **Each condition says what a wrong skip costs.** The order says once, in its
  own preamble, that a session runs a step whose condition does not hold. A
  reader takes a condition that reads as an invitation as one.
- **`R-SKL-005` carries the same two conditions in prose.** The requirement's
  body describes the order the base states and the two have to say one thing.

## Assumed

- That a session can tell which of the two ways it arrived by. Both hold the
  same file, and only the one the guide named has the brief in its context.
  Nothing measures whether a session reads that difference off its own history.
- That the path-specific half of the brief is worth the call for a session that
  arrived without it. The measure is that the brief comes from the paths, not
  what a session loses without it.
- That "touches no TYPO3 API" has an answer before the change is complete. The
  condition rests on the files a change touches for that reason. A task
  described as tooling can still end in a PHP file that calls the core.

## Wrong if

- A session arrives from the guide, skips step 3, and turns out to have needed
  something only the path-specific brief carries. A hint matched on its paths, a
  core check, or the commit step while `D-SKL-014` is not yet in the skill
  bodies. Then the route is not what makes the call already done, and step 3
  goes back to unconditional.
- A session skips the sweep on a change it then extends into code that calls the
  core. A deprecation the sweep would have returned lands in the commit. Then
  the session reads the condition off the task and not off the files, which the
  step's own wording has to fix.
- A session reads either condition as a licence. Step 3 skipped by a session
  that activated the skill from its `description`, or the sweep skipped on a
  change that touches TYPO3 API. Then narrow wording did not survive
  publication, and what is left is to take the conditions out again.
- `D-SKL-014` lands in the skill bodies and step 3 still names the commit step
  as a cost of a skip. That half is no longer true on that commit. The
  path-specific brief is what carries the condition afterwards.

## Since then

The fourth **Wrong if** fired on the commit that implemented `D-SKL-014`. Step 3
named the commit step as one of the two costs of a wrong skip. The skills that
own extension work now carry it themselves. So that half is gone from the base.
The path-specific brief is the whole of what the condition rests on afterwards,
which is what this entry said would happen.

The third **Wrong if** has since fired too. A session that activated a skill
from its own description read the base whole. It loaded the guide's schema in
the same batch as four tools it went on to use. It never called it, in 143
calls, with no word that it had passed the step over. So the narrow wording did
not survive publication, and the reading that weighs taking the condition off is
queued rather than made, because the base is installed in somebody else's
project.

## Revoked on 2026-08-11

The reading was made and the condition came off step 3, so half the statement is
no longer what the file says — and an entry a reader may build on has to be one
whose statement is true when they read it.

What the two sightings show is not the third **Wrong if**'s wording problem:
neither session cited the condition, weighed it or reported a skip. What failed
is the first **Assumed**, which said nothing measures whether a session reads
its own activation route off its history. Two now have a measure and neither
did. The step-5 half stands and moves with the reasons, so `D-SKL-034` is what
holds from here, with a different **Wrong if**.
