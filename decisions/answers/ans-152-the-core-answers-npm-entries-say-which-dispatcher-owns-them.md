---
id: D-ANS-152
title: The core answer's npm entries say which dispatcher owns them
date: 2026-09-09
status: open
coveredBy:
  - ProjectTest::anNpmCommandOnACoreCheckoutNamesTheDispatcherThatOwnsIt
  - ProjectTest::anNpmCommandOutsideTheCoreNamesNoDispatcher
---

# D-ANS-152 — The core answer's npm entries say which dispatcher owns them

**On a core checkout each npm entry in `commands` says the core runs it through
`Build/Scripts/runTests.sh`, because a sentence above the list does not reach a
caller reading the list.**

`D-ANS-031` put that sentence in the preamble and its first **Wrong if** has
fired: a session held the answer and reported the pointer as absent.

## Evidence

- **The report.**
  [`feedback/2026-09-09-182615`](../../feedback/2026-09-09-182615-on-a-core-checkout-commands-lists-the-npm.md),
  `/home/benji/projects/typo3-cms`, `claude-opus-5[1m]`. It quotes three npm
  entries out of `commands` and states that `runTests.sh` does not appear in the
  array at all, then names the five suites it actually ran, every one of them
  through the dispatcher.
- **The pointer was there and was not taken.** Re-run on 2026-09-09 against that
  checkout: the preamble to `commands` says the core's suites are run by
  `Build/Scripts/runTests.sh`, which no manifest declares, and names
  `typo3_test_run_guide`. It has stood since 2026-08-04. The session read past
  it to the list.
- **The core's own contract, read in `.checkouts/` on 2026-09-09.** `main`,
  `14.3` and `13.4` each carry an `AGENTS.md` saying "Do not invoke `phpunit`,
  `phpstan`, `php-cs-fixer`, `npm` or `grunt` directly — the wrapper supplies
  the PHP version, database service and bootstrap". `12.4` carries no
  `AGENTS.md`. All four carry `Build/Scripts/runTests.sh`.
- **The `instructions` tell a caller to trust this field.** A check recommended
  where the repository declares none is a wrong answer however sensible it
  sounds, so a caller doing as it is told runs what the target repository
  forbids.
- **The defect in this repository's own terms.** The npm entries are data and
  the dispatcher is prose. What is read as data lives where data lives, and the
  half a caller parses is the half that wins.

## Decided

- **The mark goes on the entry, not beside the array.** Each npm entry on a core
  checkout carries what the core does with that command, so the caller filtering
  the array for something to run meets it there.
- **The npm entries stay.** They are declared in `Build/package.json` and
  reporting a declaration accurately is what this field is; dropping them would
  answer a core checkout with less than the manifest says.
- **Against putting the suites into `commands`.** The field is what the
  repository declares, and `runTests.sh` is declared by no manifest — that
  absence is the fact `D-ANS-031` is built on, and a suite list written into a
  `declares` array would make the field mean two things.
- **The mark is on the dispatcher rather than on the `AGENTS.md`.** All four
  covered majors ship `runTests.sh`; only three carry the file that forbids the
  direct route, so a sentence citing the file would be wrong on `12.4` while one
  naming the dispatcher holds everywhere.
- Queued rather than made here, because it changes `src/` and the shape of an
  entry a schema declares — `D-FBK-052`.
- The card carries `normal` rather than `low`. One session, and no session has
  yet been reported losing a task to it; what raises it is that a wrong answer a
  caller is told to trust costs more than a missing one, and this one inverts
  the contract of the repository it describes.

## Assumed

- That a caller reading `commands` reads the entries and not the paragraph over
  them. This report is one such caller and its own account says so; nothing here
  measures the rest.
- That the npm scripts a core checkout declares are all ones the dispatcher
  covers. Read off `Build/package.json` against the suites `runTests.sh -h`
  lists, and not per script.

## Wrong if

- A session reports the mark as noise on every npm entry of a long list. Then it
  belongs once, above them, which is where it is today and what this entry
  moves.
- A core checkout is reported whose npm scripts are the supported route for
  something the dispatcher has no suite for. Then the mark is false on that
  entry and has to be per script after all.
- A session reports reading the marked entries and running npm anyway. Then the
  placement was not the obstacle and `D-ANS-031`'s third **Wrong if** — the
  `instructions` rather than the answer — is what is left.
