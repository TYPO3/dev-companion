---
id: D-GUI-016
title: The brief carries its hints whether or not it names a skill
date: 2026-08-19
status: open
coveredBy:
  - HintsTest::theSkillABriefNamesTakesNoHintOutOfIt
---

# D-GUI-016 — The brief carries its hints whether or not it names a skill

**A brief carries the same hints whether or not it names a skill that owns the
work. What the brief says about them settles the skill's hint step.**

In the one measured run where a brief named a skill, the session worked from the
hints and never loaded it. That reads as a block that stood in for the route.

## Evidence

- **What the brief actually carried, measured in this checkout on 2026-08-19.**
  "Register a new content element with a custom backend preview and a Fluid
  template" names `typo3-content-element-development`. That is with a
  `tt_content` override and a content-element template under a site package. It
  carries `content-elements`, `fluid-templates`, `extbase-plugin-registration`
  and `sitepackage-templates`. `omittedHints` names `tca-formengine`,
  `content-element-preview`, `sitepackage-layout`, `sitepackage-initial-content`
  and `frontend-records`, and `typo3_hint_lookup` at its ceiling for the same
  paths answers those nine in that order. So the brief carries the strongest
  four of the block rather than the block. That is what `D-GUI-007` decided and
  what the account behind this entry assumed away.
- **What the skill's own step does with it.** Step 4 of
  [`skills/base.md`](../../skills/base.md) reads the brief's own sentence.
  `HINTS_COMPLETE` says the call has happened and a second call returns the same
  hints. `HINTS_TRUNCATED` beside `HINTS_OMITTED` says what is still due and
  names it by id. The hints in a brief therefore take a query out of the skill's
  order and leave an id fetch. A brief that withholds them puts the query back.
- **The session that loads no skill is the ordinary case.**
  [`D-SKL-033`](../task-skills/skl-033-whether-a-skill-is-activated-is-the-clients-and-the-models.md)
  records a benchmark of eighty-two runs across four sweeps with `skills_used`
  empty on every row, one `typo3_task_guide` call among seventeen tasks. A brief
  that withheld on a skill name would withhold from the sessions that are
  actually there.
- **The name is not knowledge that the skill exists in the project.**
  `TaskIntents::skills()` reads `knowledge/task-intents.json`. The `skills`
  field of the output schema says a name there is not a promise that the skill
  exists in the project. What a session without it does is on the record.
  `feedback/2026-08-01-003356` built a content element with a custom backend
  preview and guessed at facts that skill's own description covers.
- **The general form is already decided.**
  [`D-SKL-034`](../task-skills/skl-034-a-step-is-skippable-on-what-the-session-holds.md)
  took the condition off step 3 because a step is skippable on what the session
  holds and never on how it arrived. Hints withheld on a routed name is that
  condition again, moved from the skill into the answer. There the session
  cannot even see it in force.
- **`D-SKL-018`'s argument does not reach the hint block.** It names one guide
  where three competed in a sentence, so it adds a pointer and removes no
  content. The block stands on `D-GUI-007`, which is about a quoted selection
  and the name of whose it is. The todo made the amendment conditional on those
  as the same argument, and they are not.

## Decided

- **The brief stays**, and this entry is the record of the question rather than
  a change. `D-SKL-018` keeps its statement and gains a pointer here.
- **A test guards the property**, so a later change cannot gate the block on
  `skills` while the suite stays silent.
- **The measurement the todo asked for is not run.** It wanted the same prompt
  through both answers. The arm it would have to build is the thing this entry
  rejects on the evidence above. That is a server that withholds hints where it
  names a skill. A run of it means that answer goes to a driven session first.
- **A recorded run was the wrong place for it either way.** `scenarios/runs/`
  holds one run per open forward review, judged against that review's criteria,
  and `bin/cli scenarios:record` takes a review id. A prompt run twice through
  two builds of this server is neither a forward review nor a contract case. So
  nothing there could have carried it.
- **Rejected: hints withheld where the brief names a skill.** It costs the
  routed session a call it does not have to make today and costs the unrouted
  session the answer entirely. It buys an activation that eighty-two runs say
  does not follow from the room.

## Assumed

- That a session with no hints in hand reaches `typo3_hint_lookup` or works
  without them, rather than loads the skill. That is the benchmark read as a
  prior about this client, and no run has happened with the block withheld.
- That what the overlap costs is tokens rather than calls, and that a session
  pays per call (`D-FBK-020`). Four quoted hints are what that trade rests on; a
  brief with a whole workflow would be a different one.

## Wrong if

- A session that loaded the skill and read the brief calls `typo3_hint_lookup`
  with the same paths anyway. Then the sentence settles nothing, the copy is the
  call rather than the text, and the settlement is what to fix.
- A run with the block withheld loads the skill. Then the hints competed with
  the route after all, and the trade this entry prices as one-sided is a real
  one.
- The brief grows to carry what the skill's other steps fetch, the deprecation
  sweep's entries, the component catalog. A session no longer makes those calls
  on the strength of one answer. Then what this entry decides for the hints
  reads as a rule about the whole brief. Where it stops is the thing to write
  down.
