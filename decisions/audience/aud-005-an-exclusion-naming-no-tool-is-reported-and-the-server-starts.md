---
id: D-AUD-005
title: 'An exclusion naming no tool is reported and the server starts'
date: 2026-08-04
status: open
coveredBy:
  - EntrypointTest::anExcludedNameNoToolAnswersToIsSaidOnStderr
  - ExcludedToolsTest::aNameNoToolAnswersToLeavesTheRealOneExcluding
  - InstallerTest::installKeepsTheEntryAndRewritesOnlyTheCommand
---

# D-AUD-005 — An exclusion naming no tool is reported and the server starts

**The server writes an excluded name that no tool answers to onto stderr at
startup, and starts with the rest of the list.**

The server read `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS` out of the caller's
environment and compared it to nothing. So a name that matched no tool took
nothing away and said so nowhere.

## Evidence

- `a4470ee` renamed typo3_project_scope to `typo3_project_describe` and
  typo3_extension_scope to `typo3_extension_describe`. Started on 2026-08-04
  with `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS=typo3_project_scope`, the server
  offered 25 tools, `typo3_project_describe` among them. Neither side said the
  exclusion no longer applied. The same hole swallows every typo in that
  variable.
- stdout is the protocol. The transport writes one JSON-RPC line per response
  and the client parses each line. So anything else printed there is a
  corruption the client reports as a broken server. stderr is the other stream a
  started server has. It is where [`src/bootstrap.php`](../../src/bootstrap.php)
  already puts the one startup failure this binary has, and where
  [driving-a-session.md](../../documentation/contributing/driving-a-session.rst)
  reads a stalled session off.
- An MCP log notification is not a channel here. There is no session until the
  client has sent `initialize`, and the server reads the list before that. A
  client that never sets a log level gets nothing.
- What an exclusion that fails to apply costs is context. Every tool the list
  can take away is read-only. The one tool that writes, `typo3_feedback_record`,
  joins past the filter in `Registry::offered()`, and no exclusion can reach it
  today.
- The same silence had a second route, measured on 2026-08-04 in a fixture
  project. `install` and `update` replaced the whole server entry. So an `env`
  block a caller had written into `.mcp.json` was gone after the next run, and
  the tools came back. That block is the only place a client configuration can
  carry `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`. Nothing this package writes ever
  names the variable, so the installer did not write exclusions; it deleted
  them.

## Decided

- The unknown names appear once, before the transport starts, with the variable
  that carried them and the tool that lists the offer.
- A caveat rather than a refusal. An exit on a stale variable trades one wrong
  word for every tool. A client launches this process, and the person who wrote
  that word is not there to read the exit code.
- No alias: the old names no longer exclude the renamed tools. `7553cb3` and
  `a4470ee` both renamed without one. An alias is a second name for one thing
  that stays forever. What it would buy is a configuration the caller now has to
  correct exactly once.
- No suggested name beside the unknown one. A nearest match by edit distance is
  a guess, and somebody who cannot check it acts on a guess in a startup
  diagnostic. That is the reason `Installer::REMAINING` says a client's
  documentation is silent rather than what the answer probably is.
- The setup commands own the command in an entry and nothing else. What a JSON
  entry already carries beside `type`, `command`, `args` and `enabled` goes back
  as it was. So an exclusion in `env` survives an `install` or an `update`. The
  TOML section the two `.toml` clients get still comes back whole, which is a
  card rather than a second half repair. That path replaces text it never
  parsed.
- `ExcludedTools::all()` stays what the caller wrote. A cut to the names that
  are real would also change what `typo3_server_scope` reports and what the
  initialize instructions claim. That is the in-band half of this and belongs to
  whoever owns those answers.

## Assumed

- A client's stderr reaches somebody. The clients the recorded runs use capture
  it into a session log, which is what makes it the channel a stalled call comes
  off. A client that discards it leaves this caveat unread.
- The registry, asked while the list answers empty, is every tool this server
  has. It is the offered list, so in a checkout without the feedback channel the
  report names an excluded `typo3_feedback_record` too. That is why the sentence
  says the server does not offer the name rather than that no such tool exists.

## Wrong if

- The caveat names a tool that is real. That would mean the registry answered in
  a state where a tool is absent for a reason of its own. The caller hears their
  correct configuration is wrong.
- An exclusion ever guards something other than context — a tool that writes, or
  one that reaches outside on its own. Then a list that silently matched nothing
  is a refusal rather than a caveat, and this entry is the wrong default.
- A session turns up where the renamed tool came back and nobody saw the line.
  The client captured stderr and showed it to no one. The report then has to be
  in-band, where the agent reads it.

## Since then

"The one tool that writes" meant two things and this entry read as if it meant
one. `typo3_feedback_record` writes into this checkout and never into the
caller's installation. So its place past the filter is a named exception
(`R-SCO-009`) rather than the hole that sentence describes. Read with the
installation meaning, the **Wrong if** stands.

What remains is this entry's open half, and it reached further than the renamed
tools. A name that is real and that no exclusion reaches tells the client the
same falsehood an unknown one does. `D-AUD-006` closed it the same day with a
cut of `all()` to what the offered list lacks. That supersedes two bullets of
**Decided** and puts the report in-band. The statement stands: the caveat is a
caveat and the server starts.
