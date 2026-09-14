---
id: D-VER-008
title: 'A declared major is proved on a second installation'
date: 2026-08-18
status: confirmed
---

# D-VER-008 — A declared major is proved on a second installation

**Where a package declares a TYPO3 major the installation does not have, this
server says how to stand a run against it up beside it.**

The upgrade workflow ends in a proof per declared combination and names no way
to make a cell exist. One session paid a full core download to find out how. The
next declined the same step three hours later because nothing said what it would
overwrite.

## Evidence

- `feedback/2026-08-18-081129`. `t3g/blog` declares
  `^13.4.15 || ^14.3 || 15.*.*@dev` with 14.3.6 installed, and the user asked
  for a TypoScript condition fix that keeps up work on 13. The session proved
  the 14 half with a render of the frontend before and after, and the 13.4 half
  by argument. What stopped it was operational rather than technical. A pin
  inside the DDEV project rewrites `.build/` and the installed state. Nothing
  says what that costs or how to get back, and an unrecoverable installation
  looked worse than an unproven claim.
- `feedback/archive/2026-08-18-074124`, the same checkout two and a half hours
  earlier, ran it. It "built a second installation under `var/v13` with
  `composer require typo3/cms-core:^13.4` and ran the suite there, which is the
  right proof but costs a full core download". Two sessions, one question,
  opposite answers. The one that ran it put the other major in a directory of
  its own rather than repinned the installation. That is the fact the second
  session lacked.
- `typo3-extension-upgrade` promises the proof and stops short of it. Its
  description offers "proving every version it claims". **Prove it on every
  version it claims** says to build the matrix from the declaration and resolve
  each cell. It says to run the repository's own commands per cell, and to name
  an unrun cell as unrun. Neither it nor `typo3-extension-testing` says how a
  cell that is not the installed one comes to exist. That skill reaches a matrix
  once, in CI.
- `bin/cli hints:probe "install a second TYPO3 major beside the installed one to run the suite against it"`
  matched nothing on 2026-08-18, out of 98 candidates. The feedback's own query
  reaches `project-extension-tests`, `project-configuration-files` and
  `browser-tests-outside-core`, none of which is about a major other than the
  installed one.
- The neighbours exist and none of them is this. `installation-upgrade` moves
  the developer's own installation to a new major for good. That is the dump
  before the first write, the wizards, "everything after it is not revertible".
  `typo3-development-installation` puts it outside itself as "a project of its
  own rather than a verb of the one somebody develops in".
  `typo3_test_run_guide` answers for a core checkout's `runTests.sh`.
  `extension/compatibility/a-declared-major-that-is-not-installed` ends where
  this starts. A read settles the shape, and "an installation on the other major
  with the package's own suite against it is what says so".

## Decided

- **Step 1a, and taken on.** What is absent is operational fact about Composer,
  the environment and the database that no session can recall. It is a procedure
  rather than a statement, so it lands as a `knowledge/documents/` page in the
  extension scope rather than as a hint (`D-FBK-043`).
- **Where the boundary runs.** Inside: whether the repository's own CI already
  covers the cell, which the session asks before it installs anything. To stand
  the other major up in a directory of its own rather than repin the
  installation the developer works in. What that writes and what it leaves
  untouched, and whether the database survives. Which checks are worth a second
  run there and which are version-independent, and what stays behind afterwards.
  Outside, unchanged: the developer's own installation and its upgrade, which
  `installation-upgrade` carries. The API question, which `D-VER-007`'s page
  settles by a read. The matrix declaration in CI, which
  `typo3-extension-testing` owns.
- **The fallback half has an answer, trimmed rather than built twice.** The
  report asks that where the second major cannot run, the static argument
  declares itself unproven and says what it covers.
  `extension/compatibility/a-declared-major-that-is-not-installed` came the same
  day, and its closing section is that. The symbols, the branch and the revision
  it was at, what the read left uncovered, and that it is unproven outright. The
  execution order the report also names is what "left uncovered" already asks
  for. A second clause for it would be one policy in two places.
- **`typo3_project_describe` does not grow a read of CI.** The report asks it to
  report the workflow matrix beside `coreConstraint`, so a caller sees at once
  whether anything covers a claimed major. What earns a tool is the round trips
  it takes off the caller (`D-FBK-027`). `.github/workflows/` is a fact the
  caller reads once from its own checkout with nothing in the way. `AGENTS.md`
  names that as what does not earn one. The page tells the session to read it,
  as its first question rather than a later one.
- **Priority `normal`, set by arrival rather than by weight.** Two sessions from
  one checkout reached this within three hours and one of them shipped an
  unproven half, which is not `low`. One repository and one session series are
  not `high`.
- **The feedback stays open behind the card**, which is what `D-FBK-017` asks of
  a judgement that turns a feedback into work.

## Assumed

- That a second installation of its own is what the procedure will recommend.
  That comes off the one session that ran it rather than off a run here. What a
  pin inside the work project rewrites is the read the card starts with.
- That the database is the expensive half of the question. Both reports name it
  and neither measured it.
- That the situation is ordinary rather than rare. `D-VER-007` rests on the same
  assumption and nothing here has counted the repositories that declare two
  majors.

## Wrong if

- The read finds that the CI of such a repository answers nearly every case.
  Then the page is one paragraph that says to push it and a procedure nobody
  runs.
- The pin inside the work project turns out to be revertible in one command.
  Then the second installation is a cost this entry wrote into the corpus for
  nothing, and the page has it the wrong way round.
- A session follows the page, stands the other major up, and cannot run the
  suite there. The harness the package declares needs the installation the page
  told it to leave alone. That would put the boundary between this page and
  `typo3-extension-testing` in the wrong place.
- A feedback reports the page read as permission to rebuild the installation the
  developer works in. That is the failure the second session avoided with an
  unproven half, from the other side.

## Confirmed on `2026-08-18`

Carried out rather than recalled, in an `E-SITE` on 14.3. The other major
resolved in a Composer root of its own with the package symlinked in. Both
suites ran there, and the installation was byte-identical afterwards. The first
assumption holds with one correction. A root of its own rather than a second
installation. That is enough to run a suite and is why a claim about a render
still costs more. The second is wrong. The database is the cheap half, and what
the run turned up instead is that the two roots resolve differently. The second
inherits no `config.platform.php`. The third **Wrong if** stands unmeasured.
