---
id: D-ANS-105
title: The unsupported answer says what would make it answerable
date: 2026-08-25
status: open
coveredBy:
  - UnsupportedTest::aNamedRootThatCouldNotBeUsedClaimsNothingAboutTheRepository
  - UnsupportedTest::aRefusalBeforeTheInstallSaysThatTheStateEnds
  - UnsupportedTest::aRepositoryDeclaringNoTypo3IsNotToldAnInstallIsPending
  - UnsupportedTest::anInstalledRepositoryIsNotToldAnInstallIsPending
---

# D-ANS-105 — The unsupported answer says what would make it answerable

**A tool that cannot reach an installation says whether the repository declares
TYPO3 with nothing installed yet. So a caller can tell a precondition from a
dead end.**

`Result\Unsupported` reports the cause, the reason, every directory the
discovery walked and both settings variables. All of it describes what the tool
tried and how it failed, and none of it says the state changes.

## Evidence

- `feedback/2026-08-24-140259`: `typo3_changelog_lookup` refused before
  `composer install` had run, and the session read the refusal as permanent. It
  answered every API question of the next two hours out of a TYPO3 core git
  checkout it happened to have. It never called the tool again after TYPO3
  14.3.6 landed in the same repository.
- Re-run on 2026-08-25, one process over stdio, from a directory with a
  `typo3-cms-extension` manifest that requires `typo3/cms-core` and no vendor
  directory. The answer is what the feedback reports, field for field:
  `cause: no-installation`, four searched directories, an empty `diagnosis`, a
  null `misconfiguration` and both variables. Nothing has changed since the
  report.
- `typo3_project_describe` in the same directory in the same minute:
  `installed: false`, `coreConstraint: "^13.4 || ^14.0"`, and the sentence
  "TYPO3 not installed here yet ... arrive once `composer install` has run". The
  sentence this feedback asks for exists in this server and rides on one tool's
  answer.
- `Instance::describe()` remembers a success and never a failure, and its
  comment says why. An installation that appears mid-session is the ordinary
  case, "the agent runs composer install ... and the caller has no way to tell".
  The server re-resolves on every call so that it picks that install up. The
  caller cached the refusal instead, because nothing said the state was one that
  changes.
- `Installation\Project` calls `Instance::project()` and nothing else does, over
  `src/` and `tests/`. `D-ANS-085` decided that boundary, "This reaches
  `typo3_project_describe` and no other tool". This is the first session on
  record to report what it costs.
- The manual half is not the gap. `ChangelogLookup::answer()` gates on
  `Changelog::directory()` before it reaches `CoreChangelog`, which `D-ANS-067`
  decided outright. Measured on 2026-08-25 against `.checkouts/13.4` with the
  host up. The feedback's own query returns one entry, the 13.3 Deprecation
  "Fluid standalone methods", `matchedIn: "body"` and `answeredBy: "packages"`.
  The six manual versions from 15.0 down to 14.0 contribute nothing. The search
  reaches a manual entry by name and by its stated title and never by the
  identifiers in its body. That is the same entry's decided line. An ungated
  manual half would have answered the same nothing.
- The corpus: `feedback/2026-08-18-070333` is the same repository state, a fresh
  clone with no vendor directory, reported by a session that read the files by
  hand. It became `D-ANS-085` and `R-PRJ-011`, which put the state sentence into
  one answer. This is the second session to hit that state and the first to hit
  it through another tool. `bin/cli feedback:list` on 2026-08-25: 34 open, none
  without a todo that names it.

## Decided

- Step 2 of the ladder, delivery. The sentence exists and reached one tool; the
  session's first call was a different one. Not step 1a, since nothing about
  TYPO3 needs a source, and not 1b, since no answer lacks a shape.
- **That** `Result\Unsupported` says which state the repository the caller
  stands in is in, and the boundary is the state rather than the fields. What
  comes out of an installed tree stays withheld, so `D-ANS-085`'s last
  **Decided** holds. `cause: no-installation` is still the whole of what those
  tools answer about the installation.
- `R-ANS-001` is not touched. What it forbids beside `unsupported` is a count to
  read as a count and a flag to read as a fact. And an empty list in place of a
  result. A statement about the precondition is none of those, and it claims
  nothing about the question the caller asked.
- Queued rather than closed on the spot. It changes `src/Result/Unsupported.php`
  and the `unsupported` object `Result\Schema` declares.
  `documentation/records/judging.rst` puts a declared schema beyond a run that
  has read only this repository.
- Priority `normal` and not `low`, because the card has its judgement. The
  sentence exists, the state already has its computation, and what remains is
  where it goes.
- Rejected: the feedback's first half read as a request to answer without an
  installation. The entry its query wanted is an installed-side body match, and
  the measurement above is what says so.
- Not decided here: which field carries the state and whether the remedy has the
  form of one. Nor whether `settings.root` gains a sentence beside the variable
  name. Those are the todo's.

## Assumed

- That the session would have asked again had the answer named the precondition.
  It says so of itself — "I assumed the earlier refusal still held. That
  assumption was never tested", and nothing else records a read of that answer.
- That the reconstruction places the directory of the report the way the real
  one placed it. `/home/benji/projects/ext-usercentrics` is another session's
  working directory, and this run did not read it. A manifest of the shape the
  feedback describes went into a temporary directory instead.
- That a caller reads the fields under `unsupported` rather than the sentence
  beside them, which `D-ANS-083` assumes for the same object.

## Wrong if

- A session gets the precondition, installs, and still does not ask again. Then
  the answer is not the lever, and the gap is something that fires on the state
  change rather than sits in the refusal.
- The answer reports the state in a repository that declares nothing about
  TYPO3, so an ordinary PHP checkout hears an install is due. Then the placement
  widened `Instance::project()`'s root rule instead of used it as it is, which
  is the way `R-PRJ-011` says this goes wrong.
- A caller reads the state as an instruction and installs a repository it was
  not asked to install. Then the refusal has become a prescription, and what the
  answer owes is the state rather than the remedy.

## Since then

The three questions this entry left to the todo settled on 2026-08-25, in the
change that carried it out. `repositoryState` is the field that carries the
state, beside `cause` and nullable. It is `installed`, `not-installed`,
`undeclared`, and null where a named root that failed left nothing searched.
`Instance::project()` serves as it stands, and `Instance::packages()` decides
which of the first two a repository is in. So a checkout that declares nothing
about TYPO3 gets `undeclared`, which is the second **Wrong if**.

What the text gains is the state. One sentence, in the one state that ends by
itself, "this answer changes once composer install has run". It says what
changes rather than what to run. `settings.root` keeps the line it had. It names
the variable that names an installation root. A sentence that tells a caller to
set it is the prescription the third **Wrong if** is about.
