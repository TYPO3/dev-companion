---
id: D-AUD-009
title: 'The entry point claims patch work'
date: 2026-08-08
status: open
coveredBy:
  - KnowledgeTest::theInvocationNotesNameTheInstallAFreshCheckoutOwes
  - ScopeTest::theEntryPointClaimsTheWorkThatEndsBeforeAPatch
---

# D-AUD-009 — The entry point claims patch work

**A session asked whether a bug still reproduces skipped `typo3_task_guide`,
because the instruction beside it says the code agent writes the patch.** Its
task ended one step before a patch.

## Evidence

- The sentence is verbatim, read again on 2026-08-08: "The coding agent writes
  the patch; this server supplies the task knowledge and workflows around it."
  It sits directly after "typo3_task_guide then gives the workflow the task
  belongs to".
- `feedback/2026-08-07-231236` quotes both and says what it concluded. The guide
  read as if it spoke to work it deliberately left alone, because the user's
  last words were "before I touch it". So a read-only triage had no workflow to
  look up. It says it would make the same call again from the same wording,
  which makes this the wording rather than the session.
- The task was `SKILL-12`'s prompt: thirty oldest unresolved issues, pick the
  first real bug, say whether it is still a thing. Backlog triage, "does this
  still reproduce" and "what would this cost" all end before a patch exists.
  `scenarios/` names all three as work this server answers for.
- The same shape twice more in one session. The session passed over
  `typo3_test_run_guide` at the moment it started to poke at `runTests.sh` with
  `ls` and `command -v`. Its description opens "Recommend
  Build/Scripts/runTests.sh commands by topic". That claims the run of a suite
  rather than what a checkout needs before one can run at all —
  `feedback/2026-08-07-231249`. `typo3_server_scope` was in the list and the
  session never called it because "the task looked legible without orientation"
  — `feedback/2026-08-07-231203`.
- Nothing routed, because no skill sat where the client reads them. That is a
  setup fault of this repository's and is why the run settles nothing about
  `SKILL-12`. What it does settle is what the tools carry alone, and the tracker
  half carried well.

## Decided

- The frame is wrong rather than incomplete. It names the division of labour,
  who writes the patch, where a caller looks for what this server answers for. A
  task with no patch at the end of it reads that as somebody else's subject.
- So the entry point claims the work that ends before a patch by name: triage,
  reproduction, and the price of a fix. Those are not a concession to one
  session; they are three of the task shapes `scenarios/` already holds cases
  for.
- `typo3_test_run_guide` claims the earlier question in the same move: what a
  checkout needs before a functional test can run, and which interpreter it runs
  under. That is the question an agent holds at the moment it reaches for `ls`.
- This does not reopen `D-ANS-061`. That entry decided the lever is the tool the
  session does call. This is the same argument applied to what those tools say
  they are for.

## Assumed

- The wording is what did it. The session says so and says it would draw the
  same conclusion again, which is strong for one reader and is still one reader.
- Claiming the pre-patch tasks does not cost the post-patch ones their clarity.
  Nothing here has measured a description that names both.

## Wrong if

- A session reports that it reached `typo3_task_guide` for a triage and found a
  checklist about a patch. That would say the frame was honest and the coverage
  is the gap.
- A report says three more task shapes named in the instructions make them
  longer and no clearer, which `R-ANS-013` already holds a budget for.

## Since then

The sentence is gone and what replaced it names the three tasks that do not end
in a patch. It displaces rather than adds, which the budget requires.
`typo3_test_run_guide` claims the earlier question in its first sentence now,
and its answer took the same order. What a checkout needs before any suite runs
opens the block where it was two of seven notes below every suite.

The first **Assumed** was then read from the other side. A session that lost a
container cycle to an argument order reports the tool as a bare name. It sat in
a deferred list whose schema the session never fetched. So the earlier session's
"the wording did it" and this one's "I never saw the wording" bound the lever
rather than the rewrite, which `D-AUD-003` already said. That feedback's
judgement rests on the corpus instead (`D-KNW-112`).
