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
`Build/Scripts/runTests.sh`. A sentence above the list does not reach a caller
who reads the list.**

`D-ANS-031` put that sentence in the preamble and its first **Wrong if** has
fired: a session held the answer and reported the pointer as absent.

## Evidence

- **The report.**
  [`feedback/2026-09-09-182615`](../../feedback/archive/2026-09-09-182615-on-a-core-checkout-commands-lists-the-npm.md),
  `/home/benji/projects/typo3-cms`, `claude-opus-5[1m]`. It quotes three npm
  entries out of `commands` and states that `runTests.sh` does not appear in the
  array at all. Then it names the five suites it ran, every one of them through
  the dispatcher.
- **The pointer was there and was not taken.** Re-run on 2026-09-09 against that
  checkout. The preamble to `commands` says `Build/Scripts/runTests.sh` runs the
  core's suites, which no manifest declares, and names `typo3_test_run_guide`.
  It has stood since 2026-08-04. The session read past it to the list.
- **The core's own contract, read in `.checkouts/` on 2026-09-09.** `main`,
  `14.3` and `13.4` each carry an `AGENTS.md` with one instruction. It says "Do
  not invoke `phpunit`, `phpstan`, `php-cs-fixer`, `npm` or `grunt` directly —
  the wrapper supplies the PHP version, database service and bootstrap". `12.4`
  carries no `AGENTS.md`. All four carry `Build/Scripts/runTests.sh`.
- **The `instructions` tell a caller to trust this field.** A check recommended
  where the repository declares none is a wrong answer however sensible it
  sounds. So a caller who does as told runs what the target repository forbids.
- **The defect in this repository's own terms.** The npm entries are data and
  the dispatcher is prose. What a reader takes as data lives where data lives,
  and the half a caller parses is the half that wins.

## Decided

- **The mark goes on the entry, not beside the array.** Each npm entry on a core
  checkout carries what the core does with that command. So the caller who
  filters the array for something to run meets it there.
- **The npm entries stay.** `Build/package.json` declares them, and an accurate
  report of a declaration is what this field is. Their removal would answer a
  core checkout with less than the manifest says.
- **Against the suites in `commands`.** The field is what the repository
  declares, and no manifest declares `runTests.sh`. That absence is the fact
  `D-ANS-031` rests on, and a suite list in a `declares` array would make the
  field mean two things.
- **The mark is on the dispatcher rather than on the `AGENTS.md`.** All four
  covered majors ship `runTests.sh`; only three carry the file that forbids the
  direct route. So a sentence that cites the file would be wrong on `12.4`,
  while one that names the dispatcher holds everywhere.
- Queued rather than made here, because it changes `src/` and the shape of an
  entry a schema declares — `D-FBK-052`.
- The card carries `normal` rather than `low`. One session, and no report yet
  shows a session that lost a task to it. What raises it is that a wrong answer
  a caller has to trust costs more than an absent one. This one inverts the
  contract of the repository it describes.

## Assumed

- That a caller who reads `commands` reads the entries and not the paragraph
  over them. This report is one such caller and its own account says so; nothing
  here measures the rest.
- That the npm scripts a core checkout declares are all ones the dispatcher
  covers. Read off `Build/package.json` against the suites `runTests.sh -h`
  lists, and not per script.

## Wrong if

- A session reports the mark as noise on every npm entry of a long list. Then it
  belongs once, above them, which is where it is today and what this entry
  moves.
- A report names a core checkout whose npm scripts are the supported route for
  something the dispatcher has no suite for. Then the mark is false on that
  entry and has to be per script after all.
- A session reports that it read the marked entries and ran npm anyway. Then the
  placement was not the obstacle, and `D-ANS-031`'s third **Wrong if**, the
  `instructions` rather than the answer, is what remains.
