---
id: D-SKL-024
title: A description names the task and leaves the steps to the body
date: 2026-08-08
status: confirmed
coveredBy:
  - SkillTest::aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement
  - SkillTest::aWorktreeTaskMatchesTheSkillThatOwnsTheCheckout
---

# D-SKL-024 — A description names the task and leaves the steps to the body

**A skill's description names the task, the sides it owns and where it stops,
and the ordered steps stay in the body.**

The description is the only part a client reads before it chooses the skill. So
a summary of the workflow arrives where the body is not yet in place and reads
as the workflow itself.

## Evidence

- obra/superpowers measured the failure in another project. A description that
  says "code review between tasks" produced one review where the skill's own
  flow specified two. Their account is that the agent followed the summary
  instead of the body. Nothing here has a measure of that kind.
- A read of all twelve descriptions on 2026-08-08 for that shape found six that
  carry it. Four are the core workflows, each of which opens with an em-dash
  clause that is the body's own order. Triage's "find the candidates, read what
  the report claims, establish against the checkout" is three of its sections.
  Patch development's clause is all six of its sections. That is "assess the
  issue, reproduce it, make the change, cover it, write the changelog entry, run
  the checks, push it". Checkout's clause is its five in the order it states
  them. That is "find the change, fetch the patch set, put it on the branch it
  targets, rebase, restore". Patch checkout carried a second one, "It stops
  rather than improvises". That is what its "Stopping is the normal ending"
  section and `references/checklist.md` own. Patch review's clause is the
  checklist rather than the order.
- The other two are one method clause each. The module skill's "where the
  implementation must match the active installation and TYPO3 version". And the
  installation skill's "so the package can actually be run, opened in a browser
  and clicked through".
- The other six name their domain and then list nouns a user types, CType
  registration, TCA, Fluid, PHPStan, `Tests/`, or situations they arrive in. The
  upgrade skill's em-dash clause reads like the four but is not one. A major
  added, a major dropped and a replacement of what one removed are three shapes
  of the request, which is what `R-SKL-010` asks for.

## Decided

- The six clauses go and every side stays. The twelve total 6383 characters
  against 7153 before, measured over `skills/*/SKILL.md` the same way the budget
  card measured it.
- What names a sibling skill stays, because it routes rather than summarises. A
  client reads a boundary sentence before the choice, and it is the only thing
  that can send the task elsewhere.
- Where a cut clause held a word a user would type, the word stays as a trigger
  rather than as a step. Patch checkout keeps the rebase onto the target branch,
  and patch development keeps the changelog entry and the push to Gerrit. Patch
  review keeps its surfaces as nouns.
- The rule goes into
  [documentation/contributing/writing-a-skill.rst](../../documentation/contributing/writing-a-skill.rst)
  beside the two that already govern a description, and not into a requirement
  of its own. Nobody can read off the file which clause is a summary. So a
  requirement would be `not guarded` and would repeat the page it points at.

## Assumed

- That what obra/superpowers measured in another client transfers to the clients
  this server sits in. No run here has shown a session that took a description
  for a body.
- That a session takes a step named in a description as the workflow rather than
  as a route into it. The opposite is arguable. A caller who reads "reproduce it
  against the branch you are fixing" may open the body to find out how.

## Wrong if

- A forward run against one of the four core skills follows its body no more
  closely than the runs before this change. Then the description was never the
  shortcut, and the 770 characters bought listing budget alone.
- A task that names one of the cut steps no longer matches its skill, "reproduce
  this core bug against main", "click through the installed site". That would
  say the clause carried the activation rather than summarised the body.

## Confirmed on 2026-08-09

The **Assumed** got its first measure. A session asked to review a change in a
git worktree read the checkout skill's description. It took it for a
branch-switch workflow, and did the fetch by hand. The review skill's routing
line named it in front of it.

What the sighting adds is the mechanism. A step clause does not only summarise
the body, it **narrows what the description names**. Every verb after the fetch
moves the branch the session stands on. So a worktree review read as actively
not its case, which is a stronger failure than a skim. The clause went, came
back in a budget trim the same day, and went again.
`SkillTest::aWorktreeTaskMatchesTheSkillThatOwnsTheCheckout` holds both halves
so the next trim cannot make the trade unseen.

## Since then

The second **Wrong if** fired on the clause cut from triage. A session searched
the backlog six times, read four candidates, and never opened the skill whose
description no longer named that step. What fired is narrower than the
statement. "find the candidates" was not a step of the task the description
names but a deliverable of its own. `D-SKL-031` settled that the day after the
cut. So a cut that reads a job as a step removes what nothing else says, and
`D-SKL-076` is the decision on that. A read of the other five skills for the
same thing found each runs one task through to one deliverable. So triage is the
case rather than the first of several.
