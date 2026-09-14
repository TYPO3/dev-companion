---
id: D-DIS-011
title: What was read from the installation lives as long as the call
date: 2026-08-04
status: open
coveredBy:
  - IconLookupTest::anIdentifierRegisteredSinceTheLastCallIsFound
---

# D-DIS-011 — What was read from the installation lives as long as the call

**A read from the installation lives for one tool call and drops when the call
returns. So no answer describes it as it was before the caller's edit.**

Both lived for the process. Between two calls is where the agent writes, so
`typo3_icon_lookup` answered "not registered" about an icon registered a minute
earlier. That is the one answer that tool exists to prevent, in the shape that
looks exactly like the answer it is there to give.

## Evidence

- `Typo3Runtime::$answer` lived for the whole process where the read was `full`.
  Where it was not, it lived for as long as the resolved console stayed the
  same. `Icons::$icons` sat on top of it with no lifetime of its own. The only
  callers of either `forget()` were the tests and the answer recorder; no
  request handler ever called them.
- A boot costs wall-clock and no tokens: the client runs this server as a
  subprocess and pays for the answer, not for the wait. A stale answer costs
  tokens twice — once for the wrong answer, once for the work done on it.

## Decided

- `Registry::call` drops the memo in a `finally`, so a tool that throws leaves
  nothing behind either. That is the single point every tool call passes
  through, and no tool calls another.
- The console resolution is not dropped with them. `Typo3Cli::resolve()`
  memoizes only successes, and a project stopped mid-session fails at the boot
  rather than answers from a stale read. A second resolve per call pays
  `ddev describe` for nothing.
- Rejected: a mtime signature over the files that carry registrations. It exists
  to save boots, and boots are the cheap half. It would have bought a smaller
  staleness window where a dropped memo removes the window.
- `Typo3Runtime::$through` went with it. It existed to re-read when the way into
  the installation changed, which cannot happen inside one call.

## Assumed

- A tool call is atomic from the caller's side: nothing writes what it reads
  while it runs, because the caller waits for it.
- A boot per call stays fast enough for use. Through `ddev exec` it is the
  slowest path and has no measure against a real project.

## Wrong if

- A task calls several installation-backed tools in a row and the boots make it
  slow enough that a caller stops to use them. Or a client times a call out and
  the agent retries it. Either turns saved tokens back into spent ones, and the
  mtime signature is what it escalates to.

## Since then

The console resolution still stays past the read, and what it memoizes has
narrowed: only a success with no limit on it. A caveated one, the console of a
stopped DDEV project reached through an interpreter of this machine, resolves
again on every call. There the second `ddev describe` is not paid for nothing.
It is what catches the `ddev start` the caveat asked the caller for, measured in
one process against `.environments/e-site-13.4` on 2026-08-04. `R-DIS-009`
carries the rule and what it costs.
