---
id: D-AUD-006
title: 'The server reports the exclusion that happened'
date: 2026-08-04
status: open
coveredBy:
  - EntrypointTest::anExcludedNameThisServerOffersAnywayIsSaidOnStderrToo
  - ExcludedToolsTest::neitherSurfaceCallsAToolExcludedThatIsInTheList
  - ExcludedToolsTest::theScopeNamesWhatTookNothingAwayAsIgnored
  - InstallerTest::codexInstallKeepsTheLinesOfTheSectionItDoesNotOwn
  - InstallerTest::codexInstallRefusesASectionItCannotRewriteWithoutDropping
---

# D-AUD-006 — The server reports the exclusion that happened

**The client hears about the tools the offered list lacks. The report names a
name in `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS` that took nothing away as that.**

`ExcludedTools::all()` was what the caller wrote, so both client surfaces
described a capability the same server had just offered.

## Evidence

- Measured on 2026-08-04 in this checkout, over one stdio session per case.
  `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS=typo3_project_describe, typo3_icon_lookup`:
  25 tools offered, `typo3_project_describe` among them. The initialize
  instructions open "typo3_project_describe, typo3_icon_lookup are left out of
  your tool list" at 1940 characters, and `excludedTools.names` carries both.
  `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS=typo3_feedback_record`: 26 tools offered,
  it among them. The instructions open "typo3_feedback_record is left out of
  your tool list" at 1920 characters, and `excludedTools.names` carries it.
  stderr says nothing, because that name is in the registry.
- [`R-ANS-013`](../../requirements/answers/ans-013-the-instructions-fit-what-a-client-keeps.md)
  caps the instructions at 2048 characters, and the exclusion prefix counts
  against it. So the false sentence displaced a true one that a client would
  otherwise keep. After the trim the same two cases measure 1916 and 1818
  characters.
- The three names that cannot shorten the list are
  [`R-SCO-009`](../../requirements/scope/sco-009-individual-tools-can-be-excluded.md)'s,
  and two of them are outside `ExcludedTools` entirely. `Registry::offered()`
  appends the feedback tools past the filter under
  [`D-FBK-042`](../feedback/fbk-042-the-read-only-boundary-is-the-installation.md).
- The TOML half of the installer, measured the same day in a fixture project:
  `.codex/config.toml` with `env = { TYPO3_DEV_COMPANION_EXCLUDE_TOOLS = ... }`
  and `startup_timeout_sec = 30` in its section had both gone after
  `install --agent=codex`, with a `Configured` message and nothing else said.
  `installTomlConfiguration` matched the section with a regex and replaced the
  match whole. `453e439` had repaired the JSON path, which now writes back every
  field it does not own. It left this one on a card rather than half done.

## Decided

- `ExcludedTools::all()` answers what is really gone, and the report derives
  rather than declares. The registry answers twice, once with the filter and
  once with this class empty. A comparison of the two lists puts every name the
  caller wrote into one of three states. An exception added to
  `Registry::offered()` therefore arrives in the report and this class needs no
  word about it. That is the reason for two questions rather than a list of the
  three protected names here.
- Nothing about `Registry::offered()` changes. `D-FBK-042` and `R-SCO-009`
  settled the behaviour; what was wrong was the description of it.
- The result stays memoized against the raw variable. The cut asks the registry,
  which asks every tool for its schemas. `all()` runs several times per answer,
  from a variable the tests change between two of them.
- stderr says the two reasons a name takes nothing away apart, because what
  somebody has to change differs. A name no tool answers to needs a correction;
  a name this server offers anyway goes out of the variable. They are one list
  in `typo3_server_scope`, under `excludedTools.ignored`, because to a client
  they are one fact: the tool is in the list it got. That answers `D-AUD-005`'s
  third **Wrong if**: the report is now in-band as well, where a client that
  discards stderr cannot lose it.
- `excludedTools.ignored` is not a required property. Absent means nothing to
  report, which is every session that set nothing.
- The TOML path keeps what it can read and refuses what it cannot. The
  classifier takes a section's lines one at a time: blank, comment, or a whole
  `key = value`. `command` and `args` change where they stand, everything else
  passes through as it is. A line whose value does not end on it fails the run
  with the file and the line number. A refusal there rather than a guess is the
  point. The outcome that must not survive is the silent deletion. A value that
  continues on the next line cannot pass through without the key that opens it.
- A full TOML parser failed. It would be a dependency, or a second
  implementation of one, for two clients and two keys. What this needs is to
  know where a line ends, which is a smaller question than what a value means.

## Assumed

- A section this package writes into has one key per line. That is what every
  client's own documentation shows and what this package itself writes, so the
  refusal is a corner rather than the ordinary case.
- The offered list is what a client sees. The report derives from
  `Registry::definitions()`. So a client shortened anywhere else, an SDK filter,
  a transport, would meet a description of a list it does not have.

## Wrong if

- A caller hears a tool is absent and finds it in `tools/list`, or hears the
  server ignored one and finds it gone. Either way the two lists the report
  derives from are no longer the ones the client gets.
- The refusal fires on a file somebody actually keeps. Then one key per line is
  not the form of these sections, and only a parser rather than a line
  classifier keeps them.
- An `install` or an `update` turns out to have dropped a line of a section
  again. The JSON path and the TOML path now make the same promise, and it is
  the promise, not the format, that a caller relies on.
