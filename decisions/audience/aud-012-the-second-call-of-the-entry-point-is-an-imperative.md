---
id: D-AUD-012
title: 'The second call of the entry point is an imperative'
date: 2026-08-19
status: open
coveredBy:
  - ScopeTest::bothCallsOfTheEntryPointAreToldInTheImperative
  - ScopeTest::theInstructionsFitWhatAClientKeeps
  - ScopeTest::theSecondCallIsAskedAgainAtTheCallersOwnActs
---

# D-AUD-012 — The second call of the entry point is an imperative

**`typo3_task_guide` is told rather than described: the instructions say to call
it, where they said what it would give.**

Two calls stood in one paragraph, one of them an instruction to the reader and
one a sentence about a tool. A run counted them eleven to one.

## Evidence

- The benchmark of 2026-08-19,
  [`D-SKL-033`](../task-skills/skl-033-whether-a-skill-is-activated-is-the-clients-and-the-models.md)
  records it: seventeen project tasks in Claude Code 2.1.234 on `claude-opus-5`,
  eleven `typo3_project_describe` calls and one
  `typo3_task_guide`.
- Both names stood in `instructions.start` of `knowledge/server-scope.json`, one
  paragraph apart, in the same context window of the same session. So the
  channel, the client, the model and the task set stay constant across the
  eleven and the one. What differs is that the first sentence tells the reader
  to call and the second says what a call would give.
- The imperative is what this server publishes already. Step 3 of
  [`skills/base.md`](../../skills/base.md) says "Run it in every session, this
  skill's own tasks included". That stands on a channel a session reaches only
  when it activates a skill. `skills_used` is empty on all eighty-two rows of
  the same benchmark.
- The call that did fire ends and hands nothing on. Read on 2026-08-19,
  `src/Tool/ProjectDescribe.php` names `typo3_rule_lookup` for the guides it
  lists and does not name `typo3_task_guide` anywhere in its description, its
  schema or its text.
- The room. Measured the same day, the worst assembly — the stale-skills notice
  with nothing excluded — stood at 2026 characters of the 2048
  [`R-ANS-013`](../../requirements/answers/ans-013-the-instructions-fit-what-a-client-keeps.md)
  holds. The rewrite costs 2 and it stands at 2028.

## Decided

- The sentence becomes "Then call typo3_task_guide for the workflow the task
  belongs to; it hands the parts that have their own workflow to the skill that
  owns them."

The mood is the whole change.
- The sentence does not move. `skills/base.md` keeps the caveat about a check
  the repository does not declare inside step 1 and the guide after it. Ahead of
  that caveat, the guide would separate "a check you recommend" from the
  commands it qualifies by a whole sentence.
- Not "in every session" beside it, which is how `base.md` puts it. It is about
  eleven more characters of the twenty-two that remained, and "the workflow the
  task belongs to" already says every task has one. A rewrite of one sentence
  displaces nothing; that addition would have to.
- Not a project answer that names the guide. It is the channel with the better
  argument on this run, the call that fired eleven times. But it is a change to
  an answer rather than to a sentence, so `todo/open/2026-08-19-140000` queues
  it rather than this entry takes it.

## Assumed

- That the mood is what the eleven to one measures. The two sentences differ in
  position as well, one run separates neither, and nothing here can drive a
  second.
- That a client which acts on the first imperative acts on a second one in the
  same paragraph. Nobody has watched a paragraph that carries two.

## Wrong if

- A run of the same shape counts the two calls again and the ratio has not
  moved. Then the mood is not what the placement bought, and the project answer
  is the lever left.
- A session reports a `typo3_task_guide` call for a task with no workflow to
  give it, and the answer costs it a turn. Then the descriptive wording carried
  a condition and the imperative overclaims.

## Since then

The last **Decided** bullet stands settled the way this entry left it, and for a
different reason. The project answer's guides listing already stands at its
foot, and four sessions have held it while it routed none of them. `D-ANS-091`
is the account, with what would make the answer the lever after all. On
2026-08-24 the sentence's second half became the acts to call again at
(`D-SKL-062`). The mood stays, so the first **Wrong if** still waits on a second
run of the counted shape.
