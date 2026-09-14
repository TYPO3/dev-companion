---
id: D-FBK-001
title: The backlog is read out rather than enforced
date: 2026-07-31
status: confirmed
---

# D-FBK-001 — The backlog is read out rather than enforced

**What `requirements/` and `decisions/` call unfinished goes into a report and
never fails a check. The bond to the queue is a line of output, not a rule.**

Both directories carry a state that means unfinished, and until today nothing
read either of them. The question was whether a visible list is enough, or
whether the check has to fail until an entry sits in the queue.

## Evidence

- What the first sweep found, on this entry's day. - 105 requirements, of which
  one is `open` and one is `not guarded`. `R-AUD-002` has stood open since the
  directory's first day on 2026-07-29, and has a contract case in `META-03` that
  names the unmet half outright. No item in `todo.md` had ever named it. Nothing
  had gone wrong; nobody had looked. - 31 decisions, of which 29 are `standing`.
  Every one of them wrote down a **Wrong if**, and one entry has ever been
  back-checked. - `feedback/` and the forward reviews feed the queue and nothing
  else does. So neither directory had a path into the order of the work at all.

## Decided

- `bin/cli unresolved:list` reads both out, `bin/cli repository:check` closes
  with the same block, and the exit code stays. Three stricter shapes fell with
  it. A check that fails while an `open` requirement has no item, a
  `not guarded` that must carry a reason, and a `standing` with an expiry. All
  three turn a legitimate state into an error. A principle no test can hold and
  a decision nothing has come back about are not defects. The third would have
  made CI red on 29 entries the day it landed, which is how a check gets
  switched off rather than answered.

## Assumed

- That a session which reads the line acts on it. The item asks for the
  judgement rather than the work, so the action can be one sentence that says an
  entry stays as it is. That is a much lower bar than a fix, and the reason the
  soft form looked sufficient.
- That the 29 `standing` decisions mostly stand because they are still true, so
  a count and the oldest is a fair summary. If a third of them turn out to be
  overtaken, the summary hid a queue.

## Wrong if

- The same id still shows with `no todo names it` after three sessions that ran
  the check. Or the `standing` count only ever grows and no session sorts them.
  Then the visible list was not the gap and the bond has to become a rule. The
  shape to reach for is the one rejected here. `bin/cli requirements:check`
  fails while an `open` entry sits in no queue.

## Since then

The command was renamed. `bin/cli unresolved:list` reads out what this entry
calls the backlog from 2026-08-04. The word itself retired because it named
three different sets at once,
[`D-FBK-041`](fbk-041-what-nothing-answers-for-is-called-unresolved.md). What
this entry decided stands: the list still reports and still fails nothing.

## Confirmed on 2026-08-02

Neither half of the **Wrong if**. No id shows with `no todo names it`, so the
first half has no subject to repeat. That says the requirement side is clean
rather than that a session acts on the line. The `standing` count fell across
four back-checks that day, so it does not only ever grow. All four came back
`tested` rather than `corrected`, which speaks to the second **Assumed**: the
entries stood because they were still true. Four is a day rather than a trend.
