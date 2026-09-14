---
id: D-ANS-037
title: 'A compound rule query is owed the section its score prefers'
date: 2026-08-03
status: open
coveredBy:
  - KnowledgeTest::aMissInsideTheCoreNamesTheWords
  - KnowledgeTest::aMissThatWithheldADocumentSaysTheBoundaryEmptiedIt
  - KnowledgeTest::aQueryThatNamesItsDocumentReachesTheSectionThatAnswersIt
  - KnowledgeTest::aSubsetIsNamedInTheWordsTheQueryWasWrittenIn
  - KnowledgeTest::anUnrelatedQueryAnswersWithNothing
  - KnowledgeTest::everyDocumentIsReachedByItsOwnTitle
  - KnowledgeTest::whatAMissOffersToAskAgainWithReturnsSections
---

# D-ANS-037 — A compound rule query is owed the section its score prefers

**A rule query longer than its topic gets the section its score prefers, and a
miss that names the words rather than the core boundary.**

Coverage decides both halves of `typo3_rule_lookup` today. It ranks and it
gates, so two words that name the document drop the section that answers below
the floor. Where nothing survives it, the answer reaches its no-match path only
when the hints missed too.

`feedback/2026-08-01-115115` reports three answers that worked. It closes with
the half nothing else in its session states: the compound queries failed and the
single-term ones worked. Read against the corpus, that is the boundary
[`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)
says a strength carries. A caller asks for a topic in the words of a topic, and
a task sentence is not one.

## Evidence

- The strength reproduces whole, re-run on 2026-08-03 through
  `bin/typo3-dev-companion` from `/home/benji/projects/typo3-cms`, the directory
  it came from. `typo3_project_describe` answers
  `core-checkout, TYPO3 15.0.0-dev, PHP ^8.5 declared and 8.5 in DDEV`. It adds
  `Extensions: none beyond TYPO3's own.` and
  `Sites: none configured below config/sites/.` Those are the report's four
  claims, in its own words. `typo3_rule_lookup "breaking change"` returns
  `## Breaking Changes` and `## Changelog Files` at 100% of the query terms. The
  first of them states the `[!!!]` marker, the changelog RST and the extension
  scanner matcher. `typo3_commit_message_guide` on the patch's own subject
  answers
  `WARNING: The summary line is 68 characters long. Below 52 characters is preferred.`
- The compound query this session recorded has its answer. It is in the sibling
  `feedback/2026-08-01-115109`, now in the archive, and
  [`D-ANS-029`](ans-029-the-scanner-matcher-is-stated-on-the-route-a-removal-takes.md)
  measured it as a miss on 2026-08-02. Re-run today,
  `removing public method extension scanner matcher breaking changelog` returns
  `## Breaking Changes`, because
  [`D-ANS-035`](ans-035-the-matcher-entry-is-owed-to-what-the-changelog-tag-claims.md)
  put the matcher sentence in it.
- The shape the report names is still there, and it is not a miss.
  `commit message summary line length` returns `## One-Time Setup` and
  `## Release Branches and Backports` of the Gerrit workflow. Both stand at
  coverage 0.525 and score 38. `## Summary Line` has score 124, and it is the
  section with the 52-character rule the same session tripped on. It sits at
  coverage 0.429 and `Documents::MIN_COVERAGE` drops it. `summary line length`
  returns it first at 0.659, and `summary line` returns it first too.
- Which two words cost it is exact. The weights are `commit` 0.92, `messag`
  1.61, `summar` 1.83, `line` 1.27, `length` 1.61. `## Summary Line` carries
  `summar` and `line`; the Gerrit sections carry `commit`, `messag` and `line`.
  So two sections that merely say the subject's name beat the section that is
  *about* the query. The score that knows the difference never gets a vote.
  `Documents::search()` sorts on coverage first and gates on coverage alone.
- Naming the document is pure cost. `Documents::searchable()` hands the matcher
  a section's heading and body, so the document title is in no searched field.
  `## Summary Line` does not contain the words *commit* or *message*, which
  stand in the title and the preamble of `typo3-commit-messages.md`. This is
  [`D-ANS-021`](ans-021-the-manual-lookup-says-why-a-short-query-ranks-better.md)'s
  finding on a second corpus, and worse in one way. There the subject term was
  merely cheap; here the searched field lacks it.
- Where a compound query does miss, the caller is told the wrong reason.
  `RuleLookup::answer()` reaches `noMatch()` only where the prose, the hints and
  the withheld documents are all empty. Where hints matched, an empty prose
  result prints `No section that holds outside the core matched "<query>"`
  whatever the scope. The session's own task sentence, *review of core patch
  replacing GD error thumbnails with SVG placeholder*, gets that line from
  `/home/benji/projects/typo3-cms`. It comes with `scope: core` and
  `withheldDocuments: []`: a boundary that did not apply and withheld nothing.
- It has already misled a judging run. `D-ANS-029` quoted that sentence back on
  2026-08-02 as what the tool answered a core-checkout query. It read it as an
  answer rather than as a claim about scope.
- The miss also drops the orientation the other path gives. `noMatch()` lists
  every document with its topics, which is what `R-ANS-006` holds this tool to
  and what a caller can ask for outright. The hint-matched miss lists neither
  those nor a sub-query that would have hit, which is what `D-ANS-016` built for
  the changelog. No test in `tests/` reaches either miss path of this tool.
- The curated callers stay as they are. `TaskIntents` puts short `rulesQuery`
  strings through the same search. The `breaking` intent's is
  `breaking change changelog`, which returns four sections today. So what moves
  here is the caller who writes their own sentence.

## Decided

- **Trimmed.** The three answers the report asks to keep reproduce in its own
  words, and to keep something is not work. The feedback shrinks to its last
  sentence and stays open behind the two cards below.
- **The miss sentence is step 4, wording.** The session called the tool
  correctly and it answered honestly. What it delivered was a reason that is
  false inside the core and orientation the sibling path already has. Queued
  rather than closed on the spot, because it is `src/`.
- **The coverage floor is a gap in the gate rather than in the wording.** The
  score preferred the right section by more than three to one, and a share
  computed over the query's own length overruled it. That is
  [`D-ANS-025`](ans-025-a-query-a-hint-carries-whole-is-not-diluted-out-of-it.md)'s
  mechanism from the other end. That entry read dilution by the length of the
  *text* and left the prose corpus alone; this is dilution by the length of the
  *query*.
- **What replaces the floor is not named here.** Three different answers exist.
  A gate that yields to the score, a rank over the largest subset that covers
  the way [`D-ANS-016`](ans-016-a-miss-names-the-query-that-would-have-hit.md)
  does, and the document title weighed into the searched fields. They have three
  different costs over the whole corpus, and the run that judged this read one
  tool. The card carries the measurement.
- Recorded against the answer rather than against the search. `D-ANS-003` keeps
  retrieval lexical and nothing here asks it not to be.

## Assumed

- That a caller writes `commit message summary line length` or something like
  it. The feedback reports the shape and records no query. So the wording is
  this run's reconstruction from what the session did: a review of a commit
  message it had just heard was 68 characters long.
- That the two Gerrit sections are not the better answer. They carry three of
  five query words and nothing about how long a summary line may be. That comes
  off the sections rather than from a measurement against a caller.
- That no other prose query is silently in this state. One turned up in a query
  for a subject the corpus states in a section named after it. Nothing swept the
  208 curated patterns or the scenario prompts against their own documents.

## Wrong if

- The floor changes and a query that used to reach nothing starts to return the
  nearest unrelated section. That is what `MIN_COVERAGE` exists to stop, and
  `Documents`'s own docblock is the promise it would break.
- The miss sentence changes and a later feedback still reports that a compound
  query failed. Then what the caller could not see was the rank rather than the
  reason, and the second card is the whole of the answer.
- A sweep finds `## Summary Line` is the only section this reaches. The prose
  corpus is small and its headings already carry their documents' subjects. Then
  this is one section that needed a word, not a property of the gate.
- The score turns out to be the wrong tie-break, because a heading weighs four
  and a long section can out-score a short one that answers. `## Summary Line`
  won on a heading match here, which is the easy case.

## Since then

The third answer landed. The title joins the searched fields at a weight of two,
and the coverage floor stays. So what the first **Wrong if** is about never
moved. The measurement covered the three over 490 queries.

Yielding the gate to the score returns the nearest unrelated section to 87
queries that reached nothing, almost every scenario prompt among them. The
largest subset that covers, admitted, returns the same 87, because a relative
floor admits its own best by construction, a question about sonnets included.
The title in the searched fields moves one first hit of 424 patterns, none of
the prompts and none of the headings. It takes nothing away from a query that
reached something.

## Since then

The miss half landed on 2026-08-03. The boundary counts as a reason only where
it withheld something. So an empty result with nothing withheld is the miss
answer whatever the hints did. Re-run, the query this entry rests on answers the
miss. Then come the per-word counts that name the subset which narrows best,
then the hints and the topic list. Both offered subsets return sections when a
caller asks them. The other path stays as it was, and a caller can reach it.
