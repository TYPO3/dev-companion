---
id: D-COD-006
title: A test writes below a temporary path that names its own process
date: 2026-08-24
status: open
coveredBy: []
---

# D-COD-006 — A test writes below a temporary path that names its own process

**A temporary directory a test writes into names its own process, because
several worktrees run `composer ci` at once and `sys_get_temp_dir()` is the one
directory all of them share.**

A fixed name there is a directory two runs write and remove under each other,
and what that surfaces as is a test failing about the thing it measures rather
than about the path.

## Evidence

- `CoreFixtureTest` wrote its variant root to
  `/tmp/typo3-dev-companion-core-variant`, under that name and no other. Driven
  twice at once on 2026-08-24, ten runs of the class failed four times: three
  reported a derived tool whose answer had moved with the root, and one threw
  out of Finder, where `Instance::packages()` walked a `typo3/sysext/` the other
  run had just removed.
- Which tool moves is whichever call the window falls in — `typo3_rule_lookup`
  in the report that queued this, `typo3_test_run_guide` in the run above.
  `Instance::packages()` reads the filesystem per call rather than once, so a
  root taken away between two calls moves the second one alone.
- With the path made unique, twelve concurrent runs of the class came back
  green.
- The suite's assertion count is not evidence of this and was read as if it
  were. Two full runs on an unchanged tree agreed exactly — 2019 tests, 66492
  assertions, and the same count in every one of the 2019 cases. What moves the
  number is the corpus: `ProseTest` asserted 32500 times over the prose files
  that day and `DecisionsTest` 13340 times over the entries. Two counts eleven
  apart are two trees, not two runs.
- Writing this entry demonstrated it. Four green runs on the branch that carries
  it counted 66492, 66513, 66538 and 66525 assertions over the same 2019 cases,
  and the only code in that branch is one line no test counts.
- Every other temporary path below `tests/` already carries `getmypid()` or
  `bin2hex(random_bytes())`; this was the one that carried neither.

## Decided

- The path carries the pid and a random suffix, which is what the rest of the
  suite already does. The rule stands in the method that builds the path,
  because that is where somebody would shorten it back; the measurement is here.
- Rejected: a check reading the other test files for a fixed temporary path.
  `R-COD-003` is deliberately not guarded, for the reason `D-COD-004` gives — a
  suite that greps itself reports on its own shape instead of on this server's —
  and this rule would be the same shape.
- `coveredBy` is empty because the failure needs two processes and the suite is
  one. What the fix rests on is the reproduction above, which nothing here runs
  again.

## Assumed

- That one run of this suite per worktree is what happens.
  `Upkeep\Fixture::write()` clears and rewrites `.fixtures/installation`, so two
  runs in a single worktree still take that directory from each other — below
  `Paths::root()`, which no other worktree reaches.
- That `sys_get_temp_dir()` is the whole of what two worktrees share. The other
  two directories a run writes, `.fixtures/` and `.checkouts/`, are named from
  `Paths::root()`.

## Wrong if

- A run fails on a temporary path a test in another worktree was holding, which
  is this one again under a different name.
- Two `composer ci` runs on a tree that did not change report different
  assertion counts. That is what this reading says cannot happen, and it would
  mean something computes the population that the corpus does not.
- `sys_get_temp_dir()` fills with variant roots. A fixed name was overwritten by
  the next run, and a unique one is not, so the `#[After]` is the whole of what
  takes one away.

## Since then

The third **Wrong if** fired, and it had been firing since the entry was
written. `sys_get_temp_dir()` held 510 `typo3-dev-companion-runtime-*` roots on
2026-09-01: `Typo3RuntimeTest` names each installation after its own process and
its `#[After]` forgot the instance without removing the directory. The second
leak was `SkillTest`'s stale project, removed on the last line of the test and
so not on a failing one — 56 of those. Both are repaired in the commit that
records this, and no other creator of a temporary root leaks: every remaining
family in that directory is one entry.

