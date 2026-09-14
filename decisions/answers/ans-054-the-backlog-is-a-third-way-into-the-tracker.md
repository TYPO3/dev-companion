---
id: D-ANS-054
title: 'The backlog is a third way into the tracker'
date: 2026-08-05
status: confirmed
coveredBy:
  - ForgeTest::anAreaIsNamedInTheCallersWordsAndMatchedAtAWordBoundary
  - ForgeTest::awordThatNamesNoAreaReadsNothingAndSaysWhichAreasExist
  - ForgeTest::theAreasAreReadFromTheProjectAndHeld
  - ForgeTest::theCountOfEverythingThatMatchedComesBackWithThePage
  - ForgeTest::theEnumerationAsksForTheOpenIssuesAndReadsThemAsFields
  - ForgeTest::theNeglectedEndIsOrderedByTwoThingsAndNotOne
  - ToolContractTest::anArgumentInAnAlternativeNamesTheOnesItExcludes
---

# D-ANS-054 — The backlog is a third way into the tracker

**`typo3_forge_lookup` answers the core project's open issues as a filtered
enumeration, and resolves the area a caller names against the project's own
categories.**

The two ways in that existed both start from something the caller already holds:
a number, or the words of a report. A triage holds neither. No number finds the
issue nobody has looked at since 2015, because nobody holds it. No wording finds
it either, because its wording is the one nobody thought of. This server could
not answer that question, and it cost the caller a hand written Redmine URL.
That is the shape `D-FBK-027` names as what earns a tool.

## Evidence

- Measured against forge.typo3.org on 2026-08-05: 2487 issues stand open on the
  core project, the oldest filed 2004-08-20. `/issues.json` takes `status_id`,
  `tracker_id`, `category_id`, `created_on`, `updated_on`, `sort` and `limit` in
  one request and answers `total_count` beside the page.
- `/search.json`, which the wording route uses, takes none of those and cannot
  order by age at all. So the third way in is a different endpoint rather than a
  parameter on the second.
- The core files its issues under 55 categories, read from
  `/projects/typo3cms-core.json?include=issue_categories` the same day.
  `/projects/typo3cms-core/issue_categories.json` answers an empty list without
  a credential, so the project is the way to reach them.
- Nobody types "RTE (rtehtmlarea + ckeditor)". The three questions this stands
  against read "rte", "backend ui" and "are there known bugs in the RTE".
- A substring match on "rte" reaches "Reporter" and "Renderer".
  `Text::containsWord` is what the rest of this server matches prose with, and
  it separates them.

## Decided

- One tool, three ways in, one record shape. `open` is a third `oneOf` branch
  beside `issue` and `query`. The entries a caller reads back are the same
  identity in all three, and `AGENTS.md` gives two tools with one output schema
  one verb.
- The record gained `category`, `assignedTo`, `createdOn` and `updatedOn`. Who
  holds an issue says whether it is free to take; the two dates are the two
  different questions a caller asks about age. A search hit answers the four
  empty, because a hit is a title and the fields are not in it.
- `total` comes back with every page. A caller shown thirty of 2487 that reads
  them as the set has measured the limit rather than the backlog.
- The tool fetches the categories and holds them for a day, and the source does
  not carry them. The core can add to a list in the code and nothing reports it,
  and the addition is exactly the subsystem somebody would filter for.
- A word that matches no category reads nothing and says which areas exist. Sent
  on with no filter it would answer with the whole backlog. That is a set about
  everything in the shape of a set about one thing.
- The tracker ids **are** written into the source, against the same argument.
  There are eleven, and `/trackers.json` is a second round trip on every
  enumeration. A twelfth tracker with no filter costs one filter, while an
  absent category costs a wrong answer.

## Assumed

- The category names the core uses are recognisable in the words a caller
  brings. "backend ui" reaches four areas, "Language Manager (backend)" among
  them. That is the fallback at work as designed, and the answer says so rather
  than hides it.
- Enumerating by age is a triage's entry point. If the question callers ask is
  "what is worth a fix", age is a weak proxy and the order earns nothing.

## Wrong if

- Callers reach the enumeration and then ask the same thing again in other
  words, which would say the filters do not carry the question.
- Reports call the category fallback wrong more often than callers use it. A
  word selects four areas where the caller meant one, in answers people act on.
- Callers read the `total` as a promise that the rest is reachable, and page
  with a higher `limit` instead of a narrower query.
- The core adds a tracker and somebody has to release this server to filter by
  it. That would say the two lists split on the wrong side.

## Confirmed on 2026-08-05

A session asked the question this entry serves for real an hour before it
landed. `typo3-core-issue-triage` told a session to call this tool with `open`,
narrowed by `category` and `tracker`, and the schema had none of it
(`feedback/2026-08-05-033805`). The session left the server and wrote the
Redmine URL by hand. Then it spent three round trips on guesses at the category
id, because `/issue_categories.json` answers 401 without a credential. That is
the shape this entry describes, reported independently of it.

Re-run today with the enumeration in place. `open: "oldest"`, `category: "rte"`,
`tracker: "Bug"` answers 5 of 23, oldest first. The area resolves to "RTE
(rtehtmlarea + ckeditor)" and both dates stand on every row. That is the call
that session could not make.

## Since then

Two of its **Decided** bullets have moved, and neither statement above them has.

A search hit no longer answers the four fields empty. What that reads as to a
caller was the next report, and
[`D-ANS-056`](ans-056-a-search-hit-is-filled-from-the-issue-it-is.md) decided
that. The reason here is that a hit is a title and the fields are not in it.
That is why it takes a further call rather than a wider parse.

The same feedback found that RTE reports sit under
System/Bootstrap/Configuration and under Link Handling as well as under RTE. So
an area filter is a floor rather than the set. The enumeration says that in its
answer where the caller used a category, which the "the areas that exist come
back" bullet did not cover. A word that matched is as much of a partial answer
as a word that did not.

## Since then

**A question this was not built for engages the first and third Wrong if.** A
caller whose words matched nothing is now routed into this enumeration, so a
duplicate check arrives beside the triage. The second **Assumed** covers the
difference and reads the other way round. An enumeration by age is a triage's
entry point. A duplicate of a defect somebody has just found is at the end
neither order reaches, because both ascend.

Measured: one area holds 437 open issues against the 26 the routing rests on. So
a caller sent here for that question reads a fraction of them.
