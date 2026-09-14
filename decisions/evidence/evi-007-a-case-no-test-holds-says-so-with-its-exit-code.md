---
id: D-EVI-007
title: A case no test holds says so with its exit code
date: 2026-08-18
status: open
coveredBy:
  - CliTest::anAppointmentComesUpOnlyWhileItsCommandFindsWork
  - ScenariosTest::aContractCaseNoTestHoldsSaysSoWithItsExitCode
---

# D-EVI-007 — A case no test holds says so with its exit code

**`bin/cli scenarios:contract` exits nonzero where the case it prints says
`not guarded`. So the todo that reads those cases is due on their state rather
than on a list.**

The todo had the list, and both halves of it went wrong at once while nothing
failed.

## Evidence

- `read-the-contract-cases-no-test-can-hold` last ran on 2026-08-02 and got a
  second read on 2026-08-18, 16 days past a 14-day cadence. `bin/cli todo:list`
  marked it due for the last two of those days and `bin/cli todo:next` handed it
  over on none. `TodoNext::perform()` reads a `bin/cli` command's exit code as
  whether there is work, and `scenarios:contract` exited 0 whatever it printed.
  It is the only one of the five recurring todos whose `**Checked:**` had not
  moved since 2026-08-03. The other four run on a command that exits nonzero or
  on one this console does not own.
- The five cases it named were not the cases that needed a read. `SITE-01` had a
  guard since `8a23def3` added
  `ScopeTest::decidingOneSitesConfigurationIsDeclinedInTheOrientation`, which is
  the boundary the todo described as unheld. Six cases had lost their guard and
  stood nowhere in it: `CORE-04`, `CORE-06`, `SKILL-11`, `SKILL-12`, `SKILL-13`,
  `SKILL-14`. Three of those carry cards of their own in `todo/waiting/` and
  three carry nothing.

## Decided

- The exit code answers the question the cadence cannot. `not guarded` in the
  `Held by` statement is 1, everything else is 0. A case that later gets a test
  stops to ask for a read without an edit to the todo by anybody. `D-FBK-012` is
  the mechanism this uses.
- `scenarios:show` keeps a 0 either way. A forward review claims its state on a
  recorded run, so `scenarios:check` answers the same question of it rather than
  whether a test exists.
- With no case named at all the command answers for every one of them, and that
  is what the todo runs. Rejected: one case in the `**Run:**` line as a stand-in
  for the rest. `SKILL-09` says in as many words that it measures the others.
  That made it the obvious sentinel, and it would have put the same failure one
  case further out. The todo goes quiet the day that one has a guard and the
  others do not.
- The todo names the criterion and the command that prints the cases instead of
  the cases. Rejected: a rewrite of the list against today's cases. That is what
  stood there and what went stale, and it would go stale again on the next case
  that changes state.
- `ScenarioReport::report()` returns nothing. It always returned 0. A return
  value nobody varies read as though the exit code were the printer's to decide
  when it is each command's.

## Assumed

- That a case's `Held by` statement is true. A person writes it by hand and the
  command reads it as data. So a case is due a read exactly as long as somebody
  says it is.

## Wrong if

- A case gets a test that does not hold what the case measures, and its line
  drops `not guarded` anyway. The exit turns 0 with the read still due, and
  nothing here can tell that from a held case. The only thing the command reads
  is the sentence. What would show it is a case that stopped in the print and a
  later session that finds the behaviour gone.
- The read the todo now asks for is more than a session does, so the session
  writes the date without a read of the cases. That is the failure a list of
  five could not have. It was small enough to look done.
