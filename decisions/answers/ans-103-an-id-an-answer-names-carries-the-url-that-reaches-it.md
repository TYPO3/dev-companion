---
id: D-ANS-103
title: 'An id an answer names carries the URL that reaches it'
date: 2026-08-24
status: open
coveredBy:
  - ForgeTest::theRelationLineCarriesTheUrlOfTheIssueItNames
  - GerritTest::aChainEntryCarriesTheUrlBuiltFromItsProjectAndNumber
  - GerritTest::aChangeNamesTheChangeItWasCherryPickedFrom
  - GerritTest::everyIdTheTextHalfNamesCarriesTheUrlThatReachesIt
---

# D-ANS-103 — An id an answer names carries the URL that reaches it

**An answer that names a record by its id prints the URL beside it, and a record
with no URL to print gains one.**

An agent that repeats an id to a person renders it as a link. A bare number
gives it nothing to render but a guess. Three lines in two tools hand over a
number whose URL the data half already holds, or holds nowhere at all.

## Evidence

- `feedback/2026-08-24-170208-an-id-in-an-answer-names-no-url-where-the-data`
  reports it from a session that read Forge and Gerrit answers to follow what a
  change resolves. It names three places and two URL forms.
- All three reproduce, re-run through the tools on 2026-08-24. `issue: "15984"`
  answers four relations with `url` in the data half. It prints
  `Relation: precedes #32756 — Bug · Closed · Massive Memory Leak in 4.5.8+ / 4.6`
  with none. `change: "95375"` answers three trailers with `url` and prints
  `- resolves #110493 — Bug · Under Review · …` with none. `change: "91563"`
  prints fifteen chain entries as
  `- 92323 · MERGED · … · chained at patch set 8, now at 10`. The record behind
  them carries `number`, `status`, `subject`, `thisChange`, `patchSet` and
  `chainedAt`, and no URL to print.
- Those three are the whole of it. A read of the text builders below `src/Tool/`
  on 2026-08-24. `ForgeLookup` prints the URL on the line under every issue
  heading and on every review line, and `GerritLookup` on every change line.
  `DocumentationLookup` and `TerLookup` print theirs. No other line names an id
  and no URL.
- The report's own account of the cost is one step out. Everything this server
  prints is reachable. What a caller composes from a bare number may not be, and
  that is a guess the answer left it to make.
- The two forms are real. `Forge::reviews()` composes
  `Gerrit::HOST . '/c/' . $number`; `Gerrit` composes
  `HOST . '/c/' . $project . '/+/' . $number`.
- The two are not interchangeable, measured against review.typo3.org on
  2026-08-24. `/c/95375` answers 302 to `/c/Packages/TYPO3.CMS/+/95375/`, so the
  server resolves the short form to whichever project holds the number. `/c/1`
  and `/c/200` answer 404, and the API confirms neither change exists — while
  `/c/Packages/TYPO3.CMS/+/1` and `/c/Packages/TYPO3.CMS/+/200` both answer 200.
  The canonical path asserts a project and renders a page whether or not the
  change is there.
- The canonical URL is in the payload the Forge side already parses, and the
  parser throws it away. Issue 110493 prints
  `- change 95375 · … · https://review.typo3.org/c/95375` while two of its own
  notes read
  `It is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95375`.
  `Forge::handles()` matches the whole URL and returns the digits out of it.
- The chain can be exact without a further call.
  `/changes/91563/revisions/current/related` answers `project` beside
  `_change_number` on every entry, read on 2026-08-24 — the same two fields
  `Gerrit` already builds a change URL from.
- `D-ANS-094` weighed what a chain entry carries and rejected the commit and the
  fetch ref for it. The URL was not among what it weighed, so this is a gap in
  that entry rather than a reversal of it.
- `R-ANS-029` is the rule next to this one and not this one. It asks that a
  named record carry enough to judge whether to open it, and all three answers
  satisfy it. Every one carries subject, tracker and status. The gap is the
  reach, not the judgement.
- One session reported it. `bin/cli feedback:list` on 2026-08-24 holds 36 open
  across five checkouts, and no other names a URL.

## Decided

- Step 4 for the two lines whose record already answers it. `relationLine` and
  `issues` print less than they print from, and the repair is what the line
  says. Step 1b in the small for the chain, where the answer has no URL at all
  and the tool builds one.
- Queued rather than closed on the spot. It touches `src/` and a declared
  `outputSchema`, which get a review rather than an improvisation.
- Priority `normal`, which takes the card off the `low` a card arrives at. What
  keeps it there against a corpus of one is that every walk meets it rather than
  one case. That is every relation, every trailer and every chain entry these
  two tools answer. What keeps it below `high` is that the caller loses a click
  and never the answer.
- The form follows what the source knows, rather than one form everywhere. A
  path that names a project asserts it, and Gerrit renders that page whether the
  change is there or not. So one composed where the project is unknown answers a
  caller with a page about nothing.
- Where the payload names the project, the tool builds the canonical form from
  it, which the change line already does and the chain now can.
- Where the tool has only a number, the short form stays. It is what
  `Forge::reviews()` has from a note that predates the move. The server resolves
  it to the right project, and it fails honestly on a number that names nothing.
- Where the note spells the URL out, the tool reads it rather than composes it
  again. That is the common case on the Forge path and it removes the guess from
  it.
- `coveredBy: []` because the work sits in the queue and the tests that hold
  this are part of it.
- Each field's name, and how each of the three lines prints its URL, belongs to
  the work.

## Assumed

- That a caller renders an id it gets as a link. That is one session's account
  of its own render, and nothing here measures what a client does with a bare
  number.
- That a URL per chain entry is worth the width. The fifteen entries of 91563
  are one line each, and a URL roughly doubles each of them.
- That `/c/<number>` still resolves. It is one measurement on one day, and the
  short form is what the Forge path has no alternative to.

## Wrong if

- A chain of fifteen becomes unreadable once every entry carries a URL, which
  would say the reach belongs in the data half alone.
- A URL this server printed answers 404, which would say the tool asserted a
  project it did not know.
- review.typo3.org no longer resolves `/c/<number>`, which would turn the Forge
  review line from a shorter link into a broken one and raise this.
- A session composes a URL from a number it got anyway, which would say the
  printed one is not where it looks.
- A fourth line arrives with the same gap, which would say this needs a check
  rather than a read.
