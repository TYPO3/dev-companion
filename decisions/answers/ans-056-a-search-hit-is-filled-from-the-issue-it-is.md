---
id: D-ANS-056
title: A search hit is filled from the issue it is
date: 2026-08-05
status: open
coveredBy:
  - ForgeTest::aPageThatCouldNotBeFilledIsStillTheHitsThatMatched
  - ForgeTest::aSearchHitIsFilledFromTheIssuesTheHitsAre
---

# D-ANS-056 — A search hit is filled from the issue it is

**One further call reads the area, the assignee and the two dates a search hit
does not carry, for the whole page. The answer does not leave them empty.**

`/search.json` answers with a title and a URL. A record that carries the other
four as empty strings states three things. The issue has no area, nobody holds
it and nothing has ever moved on it. All four of those are claims.

## Evidence

- `feedback/2026-08-05-033902`: 50 of 50 rows across two searches came back with
  `category`, `assignedTo`, `createdOn` and `updatedOn` empty. Every one of
  those issues has an area and two dates; #110159 sits under
  System/Bootstrap/Configuration.
- The session noticed only because it had already given up on the search path
  for age and gone to the Redmine endpoint by hand. Its words: a session that
  trusted the fields would have concluded these issues had no category and no
  activity.
- `/issues.json?issue_id=<list>&status_id=*` answers all four for a page of ids
  in one request, measured on 2026-08-05 against forge.typo3.org. `status_id=*`
  is what keeps a closed hit in the answer, since a search answers with closed
  issues and that endpoint defaults to the open ones.
- Age is what the caller asked the search path about. The triage skill's first
  step is entirely about which issues are old. The wording route is how a
  session reaches an issue whose number nobody holds.
- [`D-ANS-054`](ans-054-the-backlog-is-a-third-way-into-the-tracker.md) decided
  the other way on 2026-08-05: a hit is a title, so the four come back empty.
  This is the same question with one session's account of that answer in front
  of it.

## Decided

- One further call for the page, never one per hit. A search of 25 costs two
  round trips in total.
- The tracker and the status stay what the title carried, because the tracker
  words its own titles and they parse. Where the title did not parse, the fields
  fill them.
- A fill the tool could not reach leaves the hits as they came back. A second
  call that did not answer does not turn a search that answered into an outage.
  The schema says what an empty field means there.
- The maintainer had the question before this change, since the shape it changes
  dates from the same day. The answer on 2026-08-05 was to fill them.

## Assumed

- A caller who reads a hit reads the fields as facts about the issue. That is
  what the reported session did and it is what the record's shape invites, since
  the enumeration answers the same keys as measurements.
- Two round trips are cheaper than the third one a caller spends on a read of
  the whole issue to find out its date.

## Wrong if

- The second call is where searches start to fail, so a path that answered
  becomes one that half answers.
- Callers do not read the four on a search at all, which would say the round
  trip buys nothing.
- The tracker starts to serve those fields in `/search.json`, which would make
  the fill a call for something already in hand.
