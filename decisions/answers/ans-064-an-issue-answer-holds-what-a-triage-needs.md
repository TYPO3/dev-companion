---
id: D-ANS-064
title: 'An issue answer holds what a triage needs'
date: 2026-08-08
status: open
---

# D-ANS-064 — An issue answer holds what a triage needs

**Everything the session that reported it went to the checkout for was already
in the `typo3_forge_lookup` payload.** What it was not was findable: a number
without a subject, a change reference inside prose, and a journal too large to
read across candidates.

## Evidence

- Relations come back as pairs and nothing else. Re-run on 2026-08-08, issue
  15984 answers
  `[{22860, relates}, {26484, relates}, {78825, relates}, {32756, precedes}]`.
  `feedback/2026-08-07-231225` skipped all four rather than spend four issue
  reads. `#32756` is "Massive Memory Leak in 4.5.8+ / 4.6", the issue of the
  2012 revert, the single record that answers "what would this cost". The
  session found it in a git commit message instead.
- Filling them is one call rather than four.
  `issues.json?issue_id=22860,26484,78825,32756&status_id=*` answers `total 4`
  with subject and status for each, measured against forge.typo3.org the same
  day.
- Gerrit change references are in the payload and only as prose.
  `feedback/2026-08-07-231146` never called `typo3_gerrit_lookup` and never
  loaded its schema. The references sit in journal notes such as "Patch set 3 …
  It is available at http://review.typo3.org/38419". There they read as history
  already told rather than as a handle. The session answered the question from
  `git log --all --grep`. It could only report the 2021 attempt as "abandoned",
  which is all the Forge comment said.
- The journal has no bound. `feedback/2026-08-07-231213` reads two issues and
  says a triage across ten would not have been affordable. Measured: issue 14858
  answers 4090 characters of which 2573 are the journal, and 8 of its 15 notes
  are Gerrit Code Review patch-set pings. On 15984, 3 of 15.
- The same session filed the journal as what saved it.
  `feedback/2026-08-07-231137` credits it three times over. Benni Mack's note
  that calls 14858 a feature rather than a bug stopped a session from a
  verification of a misfiled feature request. Susanne Moog's 2012 revert reason
  became the design constraint it reported. Two reproductions established that
  the bug survived three majors.

## Decided

- The answer becomes legible rather than smaller. Both halves come from one
  session and neither is wrong. The journal is the most valuable thing in the
  payload, and it is the reason a session cannot afford a second issue. That is
  the shape [judging.md](../../documentation/records/judging.rst) names for step
  5, arrived here from one reader rather than two.
- So a bound on it is a parameter and never a default. A caller who reads one
  issue keeps what it has; a caller who sweeps candidates asks for less. The bot
  notes dropped is the bound worth a first place. It takes half the volume off
  14858 and removes nothing a reader would use.
- A relation carries the fields a search hit already carries. The cost is one
  bulk read per issue answer, and the caller pays it once rather than per
  relation. That is what makes this a fix and not a trade.
- A change reference becomes a field of its own that names
  `typo3_gerrit_lookup`. The information already sits parsed in the notes; the
  gap is that it is not a handle. This is the same failure `D-ANS-061` named for
  a document uri, on a different payload.

## Assumed

- One session. It is one reader over two issues, and the affordability claim is
  its estimate of a sweep it did not run.
- The bots are recognisable by author name. "Gerrit Code Review" and "Mr.
  Hudson" are the two seen; an older or renamed one would pass the filter.
- A bulk read of relations stays one call. Redmine answers `issue_id` as a list
  today, and a relation set larger than a URL can carry would split.

## Wrong if

- A session reports that it lost something because the filter took the bot
  notes, which would say the patch-set pings carry more than a URL.
- Relations come back filled and are still skipped, which would say the cost was
  never the reason.
- A report calls a structured change reference noise on issues where the patch
  is ancient and irrelevant. That would say it belongs behind a parameter too.

## Since then

The second **Wrong if** watched for relations that come back full and a session
that skips them anyway, and a session is the other outcome. In a review of a
change, it read the issue and followed the one relation the answer carried. That
led to the change that repaired it and to that commit in its own checkout. The
review changed shape. The patch under review reverts that earlier repair in
part, which decided the severity of two findings and produced a recommendation.
The session states that neither the diff nor the commit message says so.
