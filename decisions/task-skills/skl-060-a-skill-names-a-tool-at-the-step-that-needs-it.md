---
id: D-SKL-060
title: 'A skill names a tool at the step that needs it'
date: 2026-08-18
status: open
---

# D-SKL-060 — A skill names a tool at the step that needs it

**A skill names a tool at the step that needs it, and the `instructions` answer
a client that defers tool schemas.**

A session reported that it paid one schema fetch in front of every first call.
It asked for a list of the tools a workflow ends in, together near its top. All
the names arrived, in order, before the calls that needed them.

## Evidence

- `feedback/2026-08-18-074627`, a repair on a DDEV installation of `t3g/blog`.
  Four schema fetches. One batch of the four tools `skills/base.md` opens with,
  then `typo3_changelog_lookup`, `typo3_commit_message_guide` and
  `typo3_feedback_record`. Each of the three came on its own and in front of the
  call that wanted it. The session's own account is that none of the three was
  unforeseeable and that the batch was its to do.
- The names arrived. `skills/base.md` names `typo3_changelog_lookup` as step 5
  of the order the session had read.
  `skills/typo3-development-installation/SKILL.md` names
  `typo3_commit_message_guide` in the last step of **Prove it**, which the
  feedback says stood several screens before the moment of need. What the
  session skipped was step 5, not a name it never saw.
- The corpus lacks nothing. On 2026-08-18 the probe matches no hint and returns
  the index. The query was
  `bin/cli hints:probe "every tool arrives deferred so each first use costs a schema fetch the skill could have batched"`.
  The subject is a client's own mechanics, which `knowledge/` is not about.
- The placement it would reverse rests on measured runs.
  [`D-SKL-045`](skl-045-a-build-workflow-names-the-guide-at-the-step-that-needs-it.md)
  put a guide id on the step that reaches the moment.
  [`D-SKL-030`](skl-030-a-review-surface-names-the-lookup-that-can-answer-it.md)
  put the lookup on the surface it can answer, and
  [`D-SKL-014`](skl-014-the-commit-step-is-named-where-a-workflow-ends-in-a-change.md)
  the commit guide where a workflow ends in a change.
  [`writing-a-skill.rst`](../../documentation/contributing/writing-a-skill.rst)
  states the same rule for the call that reads a whole procedure: name it once,
  where the need is, and not at every mention.
- A second mention is already read as something else. The installation workflow
  discharges `typo3_server_scope`, and
  `SkillTest::everyDischargedCallIsWrittenAsOneAndRoutedNowhere` fails on a
  skill that names a discharged tool again,
  [`D-SKL-055`](skl-055-a-call-named-in-order-not-to-make-it-is-a-discharge.md).
  A fetch line listing that workflow's tools therefore either fails the suite or
  is a fetch line that deliberately leaves one out.
- The channel that survives deferral is the `instructions`, which is what
  [`D-AUD-003`](../audience/aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md)
  established. `feedback/2026-08-18-113308` reports it again from its own side.
  The tool descriptions never arrived and the `instructions` did, in full, from
  the first turn. They name six of the tools this server declares:
  `typo3_project_describe`, `typo3_task_guide`, `typo3_component_lookup`,
  `typo3_icon_lookup`, `typo3_label_lookup` and `typo3_server_scope`. They name
  none of the three this session fetched late.
- The corpus carries schema deferral twice, from two directories. Beside this
  one, `feedback/2026-08-18-113308` reports a whole session in
  `/home/benji/projects/bootstrap_package` that called nothing at all under the
  same client property. It asks for what to call for what in the `instructions`.
  That is a report about the same property and a different lever, and its
  judgement is not this one.

## Decided

- **Step 5 of the ladder in
  [judging.rst](../../documentation/records/judging.rst).** The answer was here,
  in the file the session worked from, in the right skill, and in the form of a
  step of an order. What the feedback measures is what a tool named where the
  use is costs a client that fetches a schema per first call.
- **No skill gains a fetch line.** It is a second copy of the routing at the top
  of a file no release of this server corrects. In a project where a step later
  routes elsewhere, the list still says the old thing. It is a mention the
  discharge rule already gives another meaning. It states a property of one
  client in a file the other clients read too.
- **Proposed, and the question goes up.** Whether to pay that anyway is not
  something this process may decide on its own. The card carries the question
  with what the build would cost.
- **This entry names the `instructions` as the candidate and does not change
  them.** They are the one surface a session that defers gets whole, and they
  are what `feedback/2026-08-18-113308` is about. A judgement of this feedback
  that rewrote them would decide that one without a read of it.
- **The feedback stays whole.** Its observation is a cost report and stands
  whole; what waits is its suggestion.

## Assumed

- That a session would act on a list at the top of a skill. The session had the
  order, which is the same list in the sequence of need, and batched four of it.
  So what the list adds is that the later names stand before the work rather
  than inside it. Nobody has watched a session use one.
- That the cost is the three fetches. The same session says the server was cheap
  and that its expensive loops were not server calls at all. So the round trips
  this entry weighs are the ones it counted and not the ones it paid.
- That a sentence in the `instructions` can spare them. It reaches a session
  that read the block, and whether a session batches off it is behaviour nothing
  here has measured.

## Wrong if

- A session that read the `instructions` still reports a fetch in front of every
  first call. Then the channel was not the gap, and the fetch line in the skills
  is what remains to try.
- A session reports that it batched its fetches off a list at the top of a
  skill. Then the list is delivery after all, and the cost this entry weighs it
  against is what has to be paid for it.
- The count comes back much larger than three. A client that defers charges per
  first use, so a workflow whose steps name ten tools pays ten. A session that
  reports that reports a different order of cost than the one judged here.

## Since then

The candidate this entry named landed the same day. The `instructions` index the
question each tool answers, and one entry of that is the line a second session
says would have caught it. What it cost is in `D-AUD-011`: one entry bought with
tighter sentences around it, the rest of the list not bought at all. It moves
the first **Wrong if** from something nobody could measure to something the next
debrief under such a client answers. The maintainer got the question this entry
recommended against and the answer was not to, so no skill carries such a line.
