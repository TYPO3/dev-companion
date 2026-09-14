---
id: D-KNW-113
title: Reporting a core issue is a subject this server owns
date: 2026-08-24
status: open
coveredBy:
  - ForgeTest::areasThatCouldNotBeReadAreSaidToBeUnreachable
  - ForgeTest::theAreasAreEnumeratedWithoutAWordThatHasToFail
  - KnowledgeTest::aReportBeingWrittenIsToldWhichMarkupTheDescriptionRenders
---

# D-KNW-113 — Reporting a core issue is a subject this server owns

**A `core/contribution/reporting-an-issue` document states what a new core issue
carries: its fields, the areas the project files under, and the markup its
description renders as.**

A session wrote the title and the description for a Forge issue out of its own
recollection of the form. It hedged the markup with `<pre>` because nothing here
says which one the field renders.

## Evidence

- [`feedback/2026-08-24-133626`](../../feedback/archive/2026-08-24-133626-no-route-for-authoring-a-new-forge-issue-fields.md)
  delivered a metadata table with every field name from recall and none
  verified. That is Tracker, Category, Target version, TYPO3 Version, PHP
  Version, Complexity, Is Regression, Sprint Focus. It names three guesses: the
  Category value, the `next-patchlevel` target version, and whether the
  description renders Textile or Markdown.
- Nothing here answers the question it says it would have asked.
  `bin/cli hints:probe` on the feedback's own wording matched no hint;
  `typo3_rule_lookup` for "report a bug on forge new issue fields category
  target version" matched no section; `typo3_task_guide` for "write the title
  and description for a new Forge issue reporting a core bug" answered the
  patch-development brief, down to the `runTests.sh` suites. Run on 2026-08-24.
- The markup is here once, and not as a property of the field.
  `**The comment is pasted into Forge, which renders Textile rather than Markdown.**`
  sits in `skills/typo3-core-issue-triage/references/checklist.md`, where it is
  about the comment that closes a bugfix. The session was writing a patch, so
  neither the skill nor the sentence was in front of it.
- The areas are already reachable, by accident. `typo3_forge_lookup` with
  `open: "oldest"` and a `category` word naming no area answers the project's
  own 55 names, read live — measured 2026-08-24. Both values the feedback
  wavered between are real and separate: `TypoScript`, and `Site Handling`
  beside `Site Sets & Routing`. So the enumeration it could not check was one
  call away, behind a word that has to be wrong for it to arrive.
- The `categories` field of `ForgeLookup::outputSchema()` says
  "typo3_server_scope carries the vocabulary for a caller that wants it without
  a question". It does not. A call of `typo3_server_scope` on 2026-08-24 carries
  none of `Workspaces`, `Link Handling`, `Pagetree` or `Linkvalidator`.
- One session, not a domain. `bin/cli feedback:list` on 2026-08-24 read 37 open
  feedback across four checkouts, ten of them naming Forge, and this is the only
  one about writing an issue rather than reading one.

## Decided

- **Step 1a for the fields, the target-version convention and the markup; step 2
  for the areas.** Taken on, and the answer is a document rather than a rule.
  What the session could not get was the shape of a report and the order a
  reporter fills its fields in. A sentence cannot carry that.
- The page is `knowledge/documents/core/contribution/reporting-an-issue.md`,
  which declares what it is and when to reach for it as
  [`D-KNW-057`](knw-057-a-document-declares-what-it-is-and-when-to-reach-for-it.md)
  requires. `commit-messages` and `changelog` are the siblings it sits beside,
  and `D-KNW-111` is the same shape one step earlier in the same workflow.
- **It names the call that reads the areas and does not copy their names.** The
  project administers the list and a tool already answers it live. So 55 values
  in prose is a copy that goes stale beside a source that cannot.
- `typo3_forge_lookup` stays a read. To write an issue needs a credential and
  stays the caller's, which is the fourth **Decided** bullet of
  [`D-ANS-038`](../answers/ans-038-the-tracker-is-searched-by-words-as-well-as-read-by-number.md).
  A name that promises the author direction would be the lie the feedback
  reports in reverse. What the tool owes is the areas without a wrong word. The
  route to them is the todo's, and the `categories` description's claim about
  `typo3_server_scope` is false today and gets its correction with it.
- `knowledge/task-intents.json` and the `routing` of
  `knowledge/server-scope.json` gain the authoring direction. The session's own
  account is that it never called the tool because the name reads as retrieval.
  So a document nothing routes to is the same miss again.
- Whether the triage checklist's Textile sentence becomes a reference to the
  page is the todo's, under the skill rules. A skill lands in somebody else's
  project and cannot dereference what it does not carry.
- The priority is `normal`, set by one session and by how small the reading is:
  the areas are answered already, and what is left is the form and the markup.
- Nothing holds this yet. The test declares the id in the commit that writes the
  page.

## Assumed

- That anybody can read the new-issue form with no account. The area enumeration
  is anonymous, and the required fields and their defaults may not be.
- That the field set is stable enough to be prose. Redmine's fields are
  administered per project, so a page naming them goes stale without anything
  failing.

## Wrong if

- Nobody can read the form with no credential. Then the page states what one
  person saw once, and nothing here can check it again. It belongs in the skill
  as a caveat rather than in the corpus as fact.
- A session with `core/contribution/reporting-an-issue` in its guides list still
  recalls the fields. Then the name was not what kept it out. What the feedback
  reports is a guides list nobody pulls at the point of need, and `D-KNW-111`'s
  first **Wrong if** is the same shape.
- The page carries the area names after all and they drift from what the tracker
  answers. That is the copy this entry refused, and the drift is silent.

## Since then

**The first assumption was wrong and the first Wrong if did not follow from
it.** Nobody can read the form with no account. But what the page states came
off the filed issues instead, which anybody can read again with a URL. The field
set and the trackers come back from the API. The filter settled what is
mandatory rather than the form. One field is unset on nothing filed in over a
decade and every other on the majority.

The markup was settled by rendering rather than by a setting, a reporter's
fences coming back as literal backticks and their pasted diff read as markup.
The target version is not the reporter's, so the page says to leave it empty.

The route to the areas is the wildcard rather than a wider default: it is the
tracker's own idiom and reads no issue at all. The triage skill keeps its own
sentence, which holds whether or not the triager knows the markup.
