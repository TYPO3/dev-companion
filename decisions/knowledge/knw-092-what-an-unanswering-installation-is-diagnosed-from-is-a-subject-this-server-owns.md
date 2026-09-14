---
id: D-KNW-092
title: 'What an unanswering installation is diagnosed from is a subject this server owns'
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::aFailingInstallationIsSaidWhatItWritesDownAndWhatItOnlyShows
  - SkillTest::anInstallationIsBuiltInDependencyOrder
---

# D-KNW-092 — What an unanswering installation is diagnosed from is a subject this server owns

**Nothing below `knowledge/` names the log a TYPO3 exception goes to, and no
hint carries the words a caller at an HTTP 500 arrives with.**

So the feedback goes to the queue at `normal`. The corpus does answer the
failure the session that reported hit, since two hints name exception
1396795884. It answers it by the number, which is the one thing a session with a
rendered error page in a terminal does not cheaply have.

## Evidence

- Re-run on 2026-08-18 against the corpus as it is now. `bin/cli hints:probe`
  matches nothing on `"site returns HTTP 500 where is the exception logged"` and
  nothing on `"site answers HTTP 500 exception"`. Both come back with the 91
  candidates as the index. `"var/log typo3 log file exception"` reaches
  `project-build-and-scripts`, on the word `log` in a statement about what is
  not committed.
- No hint, no skill, no requirement and no page below `documentation/` names
  `var/log`. That sentence is the nearest one: var/ is "TYPO3's writable state —
  caches, logs, sessions".
- What the corpus does answer, it answers by the number. `installation-boot` and
  `project-configuration-files` both name exception 1396795884, and a probe with
  that number reaches both. The session had roughly 24,000 characters of HTML.
- **The feedback's own remedy does not hold for the failure it reports.**
  `AbstractExceptionHandler::writeLogEntries()` returns before it logs anything
  for a code in `IGNORED_EXCEPTION_CODES`. 1396795884 is the first entry of that
  list on `.checkouts/12.4`, `13.4`, `14.3` and `main`. The trusted-hosts 500
  that cost the session six round trips shows on the page and never goes to the
  log. So an agent sent to the log for it finds nothing there.
- The log is where the rest of them are, on all four. `LOG.writerConfiguration`
  in `DefaultConfiguration.php` binds `FileWriter` with no options at `warning`,
  `FileWriter::getDefaultLogFileName()` builds `Environment::getVarPath()` plus
  `/log/typo3_<ten characters>.log` out of `defaultLogFileTemplate`, and
  `writeLogEntries()` logs an uncaught exception at `critical` with its class,
  code, file, line and message.
- The symptom axis itself is not the gap. `D-ANS-081` and `D-ANS-084` built it
  and measured it over 199 queries. A session trimmed
  `feedback/2026-08-17-212010` on 2026-08-18 to what a writer curates a hint
  with. That report counted nine debug cycles and about 45 round trips as its
  session's largest cost item.
- The fourth **Wrong if** of `D-KNW-054` has fired. The skill is out, its boot
  section names no hint, and its create branch names five without
  `installation-boot` among them. `installation-setup` in
  `knowledge/task-intents.json` does not route to it either. The session that
  reported was on the create branch and ruled the entry out by its title.
- The cost the session counted: four HTTP 500s, about ten round trips, three
  extractions written from scratch, and four HTML bodies through its context.
- The same debrief reports a second 500 from another mechanism.
  `feedback/archive/2026-08-17-211306` diagnosed a ViewHelper 500 three calls
  after it happened, so the session reached neither of the two from what it
  could see.

## Decided

- Steps 1a and 2, and queued rather than closed on the spot. The statement is
  about TYPO3 and the placement reaches a published skill, and
  [`judging.rst`](../../documentation/records/judging.rst) puts either on the
  todo's side of the line.
- `normal` rather than the `low` the card arrived at. The cost has a count and
  an open **Wrong if** fired. The same debrief reports a second failure the
  session did not reach from the symptom either.
- Not `high`. One session wrote both reports, and the repair is one lookup once
  the caller knows to make it.
- A hint of its own rather than a statement inside a task hint. What a session
  reads a failed installation from is neither the boot sequence nor the install
  sequence, and neither reported 500 happened in either. `D-KNW-054`'s third
  **Wrong if** is the other half. `installation-boot`'s patterns are what keep
  it off an install query, and symptoms loaded onto them is what that entry
  warned against.
- The trap is the statement rather than an aside beside it. To send a caller to
  `var/log` is wrong for every code the handler refuses, this feedback's own
  among them. So the hint states which exception goes to the log and which only
  shows.
- The two statements that already name 1396795884 stay where they are. Each is
  right for the procedure it describes, and whether either owes a neighbour line
  is the todo's to decide against `D-KNW-087`.
- The skill takes the routing at the step where the site is proved. Its proof
  step asks for the site to answer on both sides and says nothing about what to
  do when it does not. That is exactly where the session that reported stood.
  The boot section takes the reference `D-KNW-054` asked for in the same work.
- Curated with phrases of several words. `D-ANS-084` crosses the domain gate on
  those alone, and an agent at a 500 is regularly in another domain. The
  ViewHelper case is the worked example.
- Neither archived nor trimmed. Nothing answers either half of the feedback
  today.

## Assumed

- That an agent at a failed site asks this server at all. A writer can curate a
  corpus only for the query somebody makes, and this session made none. It went
  to curl.
- That the displayed message and the logged one are the same text where both
  exist. DDEV's generated `additional.php` sets `displayErrors`, which is why
  the page carried it. This run read nothing about the configurations where the
  two differ.
- That the ignored list is stable enough to state. 1396795884 is its first entry
  on all four checkouts and the entries below it differ between 12.4 and the
  rest. So the statement may need a bound the todo establishes.
- That one session wrote this feedback and the sixteen beside it. They share a
  directory, a model and three quarters of an hour, and nothing in a feedback
  records a session.

## Wrong if

- The statement lands and a session at a 500 still scrapes the page. The gap
  would not be in the corpus at all. A caller that stops its questions when
  something breaks is a question for the `instructions` sent at initialize. This
  entry would have answered the wrong rung.
- The new hint comes back on a query about an installation that works, or
  displaces `installation-boot` on a boot query. Its phrases would be the
  general words rather than the symptom, which is `D-ANS-084`'s second **Wrong
  if** from the corpus side.
- The reading finds the log holds the trusted-hosts exception after all — a
  writer configured below the ignored check, or a handler that is not the
  core's. The hint's central caveat would be wrong, and this entry would have
  rested on a list read in one class.
- A release drops the ignored list, or a project's own `writerConfiguration`
  turns out to be the ordinary case rather than the exception. The statement
  would be version-bound, or wrong more often than right.
- The skill names the log at its proof step and a session reports that it
  scraped the page anyway. Then the lever is the wording rather than the
  placement, and this is step 4 of the ladder.

## Since then

The hint stands and the third **Assumed** has its answer. The ignored codes, the
default writer configuration and the log path template are identical on all four
checkouts, so no statement carries a bound.

The display half turned out to be the sharper statement and the entry did not
have it. A caller off the development address gets the production handler and an
empty message. That handler makes exactly three exceptions, the first of them
the code the logger refuses. So the trusted-hosts failure shows whatever the
configuration and goes to no log, and every other uncaught exception is the
other way round. The two halves invert, which is what the hint states.

A PHP fatal is the boundary the reading added: the error masks exclude it, so it
reaches no handler and a caller told to read the log finds nothing.

## Since then

The first **Assumed** has its answer and the first **Wrong if** did not fire. A
session that hit this failure asked this server rather than went to curl, and
what answered was the neighbour hint fetched by id. So an agent at a failed site
does ask, and the session reached the case from the corpus rather than from the
page.

That is a session's report rather than a recorded run, and it does not say this
entry's own hint was what reached it. What still cost something is the step
after the diagnosis, which `D-KNW-120` carries. This hint says what a failure
writes down, not what a value resolves to.
