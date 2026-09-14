---
id: D-DOC-034
title: A recording is answered from the checkout the command makes
date: 2026-08-18
status: open
coveredBy:
  - CheckoutsTest::aGitThatCannotAnswerReportsNoDifference
  - CheckoutsTest::bothKindsOfChangeAreCarried
  - CheckoutsTest::everyEntryGitReportsIsCarried
---

# D-DOC-034 — A recording is answered from the checkout the command makes

**`bin/cli tools:record` refuses a core checkout below `.checkouts/` that
carries anything `bin/cli checkouts:update` did not put there. It does not
record what such a tree happens to answer.**

The first root is a checkout because that is the one installation this
repository can recreate, which is what makes the recording repeatable
(`D-DOC-006`). Nothing held the tree to what the command makes, so a
`composer install` in it changed the recorded answers and no reader could tell.

## Evidence

- All four checkouts on this machine carry an installed `vendor/`, which nothing
  in this repository writes: three of them from 2026-08-14 and `main` from
  2026-08-03. What that leaves is six entries in `.checkouts/14.3` as of
  2026-08-18: `.cache/`, `bin/`, `index.php`, `typo3/sysext/core/bin/`,
  `typo3temp/`, `vendor/`. The core's own `.gitignore` ignores every one of
  them, so `git status --porcelain` calls the tree clean and `--ignored` is what
  reports them.
- `bin/typo3` is one of the two paths `Typo3Cli` probes, so the checkout has a
  console again. The installation-backed tools then record a Doctrine exception
  about an absent database in place of the shapes the recording exists for.
  Those are five that answer `answeredBy: "packages"` and two that answer
  `unsupported` with `installation-not-answering`.
- The recording in `73cff0ab` ran against a 14.3 tree rebuilt from that
  checkout's index, by hand and outside any command here. That is the cost this
  settles: it is the only way the pages could come out at all, and nothing said
  so.
- `git status --porcelain --ignored` collapses an ignored directory to one entry
  and takes 0.095s on that checkout, so a question before every recording costs
  nothing.

## Decided

- The command that has the requirement states it, at the moment it matters.
  `tools:record` asks git what the root carries beyond its index and exits 2. It
  names what it found, the `git clean` that takes the checkout back, and the
  other way out, an installation of the caller's own.
- Only a root below `.checkouts/` gets the question. Those are this repository's
  own and one command makes them. A root somebody named is theirs, and whether a
  recording from it is reproducible is a question for whoever commits it.
- Anything beyond the index refuses, not just an installed console. A recording
  is reproducible from `checkouts:update` or it is not. An edited tracked file
  reaches an answer exactly as an installed one does. Which of them a tool
  happens to read is not knowable from here.
- Rejected: a `checkouts:update` that takes such a checkout back to what it
  makes. Its stated job is to create and fetch. A clean would delete a
  `composer install` somebody made to run the core's own tests, as a side effect
  of a branch update. That is 203 MB and several minutes in `.checkouts/14.3`.
  It also fixes the state at a moment nobody records at. Install again
  afterwards and the recording is wrong with nothing to say so.
- Rejected: a third written root, a pristine tree this repository produces per
  run the way it writes `Fixture` and `CoreFixture`. Those two exist as writes
  because they are small; a core checkout is 181 MB without its packages, and
  `checkouts:update` already makes exactly this tree. A second copy to defend
  the first against an install into it is a concept where a sentence does.

## Assumed

- The check reads a checkout below `.checkouts/` and nothing else, so a refusal
  there is never a false one. An editor that writes `.idea/` into a checkout
  somebody opened to read core sources would refuse a recording that would have
  been correct. The remedy stands stated rather than automatic.

## Wrong if

- A session meets the refusal and cleans the checkout to get past it, and the
  `composer install` it removed was somebody's work. Then what a checkout is for
  is in dispute and this is the wrong end to have settled it at.
- Somebody wants a recording on a machine where the checkout has to stay
  installed. That is the same person who verifies knowledge with the core's
  tests and records the tool surface. Then the two uses need two trees and the
  rejected third root comes back.

## Since then

- 2026-08-21: the recording's own sentence said what the uninstalled checkout
  costs but not why, and it read as a property of core checkouts. `.checkouts`
  is a worktree with no install in it, and the core monorepo declares
  `bin-dir: bin`. So the two paths the reason listed as absent are exactly the
  ones `composer install` would write. `Typo3Cli::reason()` now says the
  dependencies are not installed and names the absent autoloader wherever the
  console is absent and no autoloader stands beside it. The preamble of every
  recorded page carries that. Held by
  `Typo3CliTest::aCheckoutThatWasNeverInstalledSaysThat`
  and its counterpart for an installed root whose console sits elsewhere.
