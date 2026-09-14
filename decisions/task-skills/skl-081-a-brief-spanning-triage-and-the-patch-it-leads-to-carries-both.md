---
id: D-SKL-081
title: A brief spanning triage and the patch it leads to carries both
date: 2026-08-27
status: open
coveredBy:
  - SkillTest::aBriefThatTriagesAndThenFixesCarriesBothWorkflows
---

# D-SKL-081 — A brief spanning triage and the patch it leads to carries both

**Where a task finds an issue on the tracker and then fixes it,
`typo3_task_guide` names both workflows in order. It keeps the steps the patch
owes.**

Two sessions asked for exactly that and reached neither skill. The brief that
does match the triage intent strongly loses six of the patch's own checklist
items, the commit message among them.

## Evidence

- **The session.**
  [`feedback/2026-08-26-223325`](../../feedback/archive/2026-08-26-223325-the-stated-entry-point-and-both-fitting-skills.md),
  `/home/benji/projects/typo3-cms` on `claude-opus-5[1m]`, sent to "please find
  1 old forge issue and fix it" and then narrowed to Extbase. Both
  `typo3-core-issue-triage` and `typo3-core-patch-development` stood in the
  listing and the session invoked neither. It loaded `typo3_task_guide`'s schema
  in the first `ToolSearch` call and called the tool in none of the roughly
  eighty-five that followed.
- **The second session** is
  [`feedback/2026-08-24-163220`](../../feedback/archive/2026-08-24-163220-both-skills-matching-this-task-stayed-shut-for.md),
  two days earlier in the same checkout. "bitte suche forge issues im asset
  renderer bereich", then one that is easy to fix and that tests can prove. It
  found the issue, wrote the patch, the tests and the changelog entry, and
  opened no skill. `D-SKL-076` came from it and read the backlog half alone.
- **The route does not reach the skill.** Measured in this worktree on
  2026-08-27: `typo3_task_guide` with
  `task="please find 1 old forge issue and fix it"` matches `reporting` and
  `triage` weakly and answers `skills: []`. "find an old forge issue and fix it"
  and "search forge issues in the asset renderer area" answer the same. What
  `triage` holds for those words is `forge` and `old issue` in its `matchWeak`,
  and a weak intent names no skill (`D-SKL-023`).
- **The needle alone is the wrong lever, measured on a brief that already
  matches.** `fetch another old issue from Forge, create a branch, work it off`
  is `D-SKL-078`'s own worked example, matches `triage` strongly and ends in a
  change. Its brief carries 7 checklist items against the 12 of "fix Forge 15984
  in the FormEngine", and what is gone is the patch half. That is the target
  branch, the deprecation sweep, the focused patch, the narrowest useful test
  coverage, the targeted test run, and the message from
  `typo3_commit_message_guide`. `triage` carries `changesNothing`, so a strong
  match makes the whole brief one that writes nothing (`D-SKL-039`).
- **What the session reconstructed by hand is that list.** It reports
  test-first, `cgl`, `phpstan` and a commit message that obeys the 52 and 72
  column rules, all derived from the core checkout's own `AGENTS.md`. It names
  the two things that file did not carry: the branches a `Releases:` trailer
  takes, and whether the change owes a changelog entry. Both sit in the patch
  half of the brief it never got.

## Decided

- The judgement is
  [`documentation/records/judging.rst`](../../documentation/records/judging.rst)
  step 3, routing, and the lever is the `changesNothing` fork rather than a
  needle. A brief may triage and then change something, and the fork has one
  slot for it.
- **The answer names both skills, in order.** Triage is the front half and the
  patch workflow is what the crossing hands over to
  ([`D-SKL-022`](skl-022-a-handoff-between-skills-is-an-instruction-rather-than-a-closing-sentence.md)),
  so the answer states the order rather than leaving the caller to pick one of
  two names.
- **The patch skeleton stays.** A brief whose words name a change keeps the
  items that change owes, whatever else it recognized. That is the third **Wrong
  if** of `D-SKL-039` read from the other side. It watches for an author's brief
  that loses its route to a review. This is an author's brief that loses its
  checklist to a triage.
- Against a third skill. `D-SKL-005` settled that core contribution earns two,
  and the gap is the order across them rather than a workflow neither owns.
- Against a promotion of `old issue` to a strong needle on its own, which is the
  shape the feedback's own suggestion has. Measured above, it would make every
  "fix an old issue in X" a brief that writes nothing.
- **Queued rather than made here.** The route is in `src/`, and the two
  descriptions the same card carries are a tool's contract and a skill's.
- The priority is `normal`: two sessions, and this is how people ask for core
  contribution work.
- The two description halves are recorded where they belong — the triage
  description's closing clause at `D-SKL-076`, `typo3_task_guide`'s opening at
  [`D-AUD-014`](../audience/aud-014-a-description-opens-with-what-the-callers-own-route-cannot-do.md).

## Assumed

- That the session would have called `typo3_task_guide` had its description
  earned the call. It called it at no point, so the measure of what the route
  answers stands against a call nobody made. That is why the description half
  rides on the same card rather than behind this one.
- That a session reads two names in one answer as an order. `D-SKL-013` put one
  name there and nothing has measured what a second does.

## Wrong if

- A session gets both names and enters the second one only. Then the crossing is
  the handoff `D-SKL-022` watches and not the route.
- A feedback reports the triage half as noise: a caller with the issue already
  in hand, sent to establish what it still claims anyway.
- A brief of this shape opens nothing once both names arrive. Then no answer is
  what the choice rests on, and it is `D-SKL-033`'s count.
- A brief that files reaches the triage skill through one of the three needles
  added below. `write a new forge issue` is the shape that comes closest and
  does not, measured on the corpus in the section under this one.
- A review gets the patch skeleton because the request quoted the change it is
  about. That would say `patch`'s needles read the words of somebody else's
  work, which is the failure `D-SKL-039` names from the other side.

## Since then

The three levers exist and a measure over twenty-five briefs covers before and
after. Four briefs that answered no skill now answer both, and the ones that did
not move are the ones the entry predicted. The briefs that file keep their own
intent, because the plural and the adjective separate an issue taken from one
written. The review briefs answer exactly what they did.

A third session then lost on a preposition — two of the four a session might
write were in the strong list and two were not — so the list is the finding
rather than the fix. A measure beside it shows what the needles cannot reach. A
brief that knows it is core work and declares a change type still names no
workflow, because the guide reads only the sentence. `D-SKL-082` decides that
half.
