---
id: D-SKL-001
title: 'The order a task starts in is one file'
date: 2026-08-01
status: confirmed
coveredBy:
  - SkillTest::theBaseFixesTheOrderEveryTaskStartsIn
---

# D-SKL-001 — The order a task starts in is one file

**`skills/base.md` holds the order every task starts in, and each published
skill gets a copy of it rather than a share.**

The order is project scope, extension scope, task guide, conventions, and only
then the checkout.

Four recorded `REVIEW-01` runs of the same prompt in the same checkout took it
from a review this server took no part in to `covered`. The thing that decided
each step was never the wording of an instruction. It was where the read of the
checkout sat relative to everything else.

## Evidence

- Run 2 activated the skill and followed two of its seven evidence steps. Run 3
  read the skill's checklist in its first twenty seconds, then ran
  `find . -type f` and spent five minutes on the result before it called
  `typo3_task_guide` or a single conventions lookup. It filed translations under
  "assessed and clean" with `source-language="de"` on screen, because it had
  asked `typo3_translation_domain_lookup` and taken a runtime answer for a
  verdict. A comparison of the other four skills then showed the arrangement was
  everywhere. "inspect the checkout" stood at step 2 of 6 in content-element, 3
  of 6 in documentation, 3 of 7 in testing. Each had the conventions lookup
  behind it. Run 4, against one base file, walked the order and produced both
  findings three runs had missed.

## Decided

- `skills/base.md` holds the order: project scope, extension scope, task guide,
  conventions per subsystem, and only then the checkout. It holds the two things
  no skill should re-derive. A runtime lookup reports what the installation
  registers and never whether it is right. A returned rule judges the code that
  already exists as well as the code about to come. `Installer` copies it into
  each published skill as `references/base.md` rather than shares one file,
  because a skill lands in someone else's project alone. Each `SKILL.md` states
  only what it adds. Two tool defects found the same way got a repair rather
  than a workaround. The tool dropped an identifier that reached
  `addRecordType()` through a variable in silence, and nothing reported what an
  extension does not ship.

## Wrong if

- ~~`REVIEW-02` in an extension repository shows a session that outruns the base
  again. That would mean the order holds only where a checklist reinforces it.
  The conformance skill carried it rather than the base.~~ Fired on 2026-08-02
  in a self-reported call log rather than in a recorded run. The checklist is
  what did not help. The maintainer declined the `REVIEW-02` that would settle
  it.
- ~~Or the base grows. It bears the load because it is short enough for a read
  before the first call. Every sentence added to it is one the read can
  swallow.~~ Fired six times, from 496 words to 1531. What each growth bought is
  the ledger below.
- It is also unproven for four of the five skills. Their order got its
  correction on the strength of a defect measured in the fifth, and no forward
  run has touched them.

## Confirmed on 2026-07-31

`REVIEW-02` ran in `bootstrap_package` and the base was not outrun. The skill
activated on its own, and the session read `references/base.md` and the
checklist three seconds later. The three scope calls followed, and the session
wrote the six-surface list out 22 seconds before it opened the first project
file. So the order survives a checkout of another kind, by the same skill. The
four whose order changed without a forward run are still unproven, and the base
is still short.

## Since then

The **Wrong if** got a second answer from a strength, and it lands in both
halves. The session outran the base and read past one of its steps. A session
whose own account is that the order fit perfectly named the workflow minus step
3. Its tool log puts a glob and fifteen file reads ahead of the first hint
lookup. The installed copy carried the step, which carries a date and settles
that. Re-run, the call that step makes answers with the lookups the caller has
just read and names no workflow at all. That is what a second session reported
from the other side. One skipped step 3 and reported no loss, one ran it and
reported no gain. So the question is what step 3 is for once a task skill is in
place, and the two answers run opposite ways. This file says what the call is
worth, or the tool names the workflow it claims to.

## Confirmed on 2026-08-02

A second read of the same run came from the call log rather than from the
strength beside it. The log is the stronger artifact. It carries numbers, and
the session filed it while the account was still under way. It records the base
outrun at the one step the base exists for. Steps 1 and 2 in place, step 3 never
run, then a glob and fifteen reads before the first lookup. That is worse than
the **Wrong if** predicted, which expects the order to survive where a checklist
reinforces it. The checklist here carried the rule a second time in its own
words.

The evidence is a report rather than a transcript. What reproduces is the
server's half, and step 3 is the one that would not have paid. The call matches
no intent for an audit. It hands back a brief for a change to a package to a run
told to change nothing.

## Confirmed on 2026-08-02

**Stopping is still right when the server is one process away, and the base does
not learn the way round.** Three things settled it. The base cannot carry the
route. Every published skill gets one file byte-for-byte, while the path to the
binary is a property of the machine. What the hand-driven route produced is on
record. The same session pasted a live encryption key into this repository,
which is what it costs to act as your own client. And a stop reaches somebody
who can act. What remains is not a skill question. The install writes the entry
and never says a callable tool is one step further on (`D-DIS-009`).

## Since then

The question got its other side, from a session that had no skill at all. It
looked nothing up until the user demanded it, and the route through the server
ends before the skill. The entry point was in the text it got, and it hands over
nothing. Re-run, the guide answers with the two hints that session spent its
evening on guesses at, and names seven tools, none of them a skill.

So the two answers this question holds open are not the same size. A sentence in
the base about what the call is worth does nothing for a caller that arrived
without a skill. Only the other answer reaches this session. On the ladder that
is step 3 — the skill exists, the payload is in the guide's answer, and nothing
joins them.

## Since then

The person the session asked answered the question on 2026-08-03, and it is the
second of the two. `typo3_task_guide` names the workflow step 3 says it returns
(`D-SKL-013`). The base stays as it was, which is the half worth a record. The
answer that would have grown this file is the one the maintainer turned down.
What it cost instead was five intent entries and thirty lines in `src/`.

## Confirmed on 2026-08-22

Seven readings held the rule and changed nothing in it. Five are the growth this
**Wrong if** watches, and each bought one thing. Those are what a finding rests
on, the deprecation sweep, and the precondition above the order. Then the
changelog's own axes in place of a query set, and the end of the manual as where
a PHP identifier goes. One is a strength from a model nothing here has measured,
and one ran step 3 and reported no gain. Two of the fifteen published skills
carry a recorded forward run, which is what the third **Wrong if** bears on.
