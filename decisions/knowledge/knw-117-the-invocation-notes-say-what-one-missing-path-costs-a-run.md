---
id: D-KNW-117
title: 'The invocation notes say what one missing path costs a run'
date: 2026-08-24
status: open
coveredBy:
  - HintsTest::theInvocationNoteSaysWhatOneMissingPathCostsARun
---

# D-KNW-117 — The invocation notes say what one missing path costs a run

**Nothing validates the paths a suite gets, and phpunit resolves all of them
before it builds anything. So the first one that does not exist ends the whole
run.**

A caller that assembles a path list learns that an entry is not there when it
pays a container start for it. The corpus says how to narrow a run and nothing
about what a wrong path in that list costs.

## Evidence

- Measured on 2026-08-24 with this repository's own phpunit, 11.5.56 — the
  version `.checkouts/13.4` and `.checkouts/14.3` pin. Handed a test file that
  exists followed by `typo3/sysext/tstemplate/Tests/Functional`, it printed
  `Test file "typo3/sysext/tstemplate/Tests/Functional" not found` and ran
  nothing, the file that exists included.
- The mechanism is `PHPUnit\TextUI\Configuration\TestSuiteBuilder::build()`. It
  runs `realpath()` over every CLI argument into one list before it builds a
  suite. The first argument that resolves to `false` throws
  `TestFileNotFoundException`, whose message is that line. Read on 10.5.63,
  11.5.56 and 13.3.0, what `.checkouts/12.4`, `.checkouts/13.4` with
  `.checkouts/14.3`, and `.checkouts/main` pin in their `composer.lock`. The
  resolve loop is the same on all three.
- The script checks nothing of its own. On all four covered branches
  `functional)` builds
  `COMMAND=(bin/phpunit -c Build/phpunit/FunctionalTests.xml --exclude-group not-${DBMS} "$@")`
  after `shift $((OPTIND - 1))`. The script starts the redis and memcached
  containers, and the database container where the DBMS has one, before that
  command runs.
- The list of system extensions is what this rejects. On `.checkouts/main` at
  `3cbdea24dd`, five carry no `Tests/Functional` at all — `belog`,
  `filemetadata`, `opendocs`, `reports` and `tstemplate`. One commit changes
  that list, and one `ls` in the caller's own checkout answers it.
  [`D-FBK-027`](../feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)
  names a fact of that shape as what does not earn a place here.
- It settles the second **Assumed** of
  [`D-KNW-112`](knw-112-the-invocation-notes-say-where-runtests-sh-stops-reading-its-own-options.md),
  which wrote the neighbouring note without running the failing command. The
  message is phpunit's, from `TestFileNotFoundException`, and the two notes
  describe two causes of one line.

## Decided

- The statement goes into `invocation.notes`, beside the option-parse note that
  produces the same message from the other cause. Both are about what phpunit
  gets, and the reader who meets the line needs the pair.
- What the note carries is the mechanism and the price. Nothing checks the
  paths, one that does not exist ends the run, and the paths beside it never
  run. The container start is already paid when that happens.
- The five system extensions are not written down, and neither is any other
  count of them. The note names the case, a system extension with no
  `Tests/Functional`, and leaves the enumeration to the checkout.
- The statement is unbound. The resolve loop is the same on every phpunit the
  covered branches pin, so no `since` and no `until`.
- A test that names this entry holds the note, as with the notes beside it.
  Nothing sweeps the wording, so what a test holds is that the mechanism, the
  all-or-nothing and the cost are still stated.

## Assumed

- That the container start is what the caller pays. The reporting session's
  account is that the run "died before a test ran"; nothing here timed one.
- That a caller that assembles a list reads the notes before it runs. Every note
  in this block rests on that, and the measure is a session that reached
  neither.

## Wrong if

- A session reports the same failure after it read the answer. Then the cost
  belongs beside the targeted command rather than among the notes.
- A phpunit the core pins no longer resolves the list up front and runs the
  paths that exist. The note would name a cost nobody pays any more.
- A session reads the two notes as one trap and drops a path that was there,
  when the cause was the option written after it. Then the shared message needs
  one note that separates the causes rather than two that share it.
