---
id: D-ANS-010
title: '"Does it still work" is a question for the manual, not the changelog'
date: 2026-08-02
status: open
coveredBy:
  - ScopeTest::everyToolNamedInTheScopeExists
  - SkillTest::theChangelogsSilenceIsNotAnAnswerAboutWhatStillWorks
---

# D-ANS-010 — "Does it still work" is a question for the manual, not the changelog

**The routing sends "Does this still work in version N" to the manual for that
version, not to the changelog. The changelog is silent on everything nothing
changed.**

A changelog records change events. A pattern that has worked unaltered for ten
majors has no entry. So an empty result reads as "nothing found" where the
correct answer is "still supported".

## Evidence

- `feedback/2026-07-31-174524`, re-run on 2026-08-02 against
  `/home/benji/projects/bootstrap_package`. `typo3_changelog_lookup` with
  `query: "BackendLayout"` returns 11 entries now rather than the one the
  feedback reports. `D-ANS-006` landed since, and the query reaches "backend
  layout" in every spelling. None of the 11 answers what the session asked.
- `typo3_documentation_lookup` with `targetVersion: "14"` and the query
  `backend layout` returns the two pages that do answer it, first and second. A
  read of the TSconfig one with `page` settles it in that call. The manual
  documents `identifier` as what the page content DataProcessor addresses a
  column by, "a more meaningful representation than just colPos". `colPos`
  carries the content elements. The session read `GridColumn.php` in the
  installed core by hand and still recorded the finding as unverified.
- `bin/cli hints:probe "BackendLayout"` reaches nothing about backend layouts,
  and neither `knowledge/` nor `skills/` carries the words at all. That is not
  the gap. The manual owns this subject and answers it, so there is nothing to
  write here, only something to point at.
- The routing block pointed the other way. It routed `typo3_changelog_lookup`
  for "before asking what a version changed **or whether an API is still
  there**". The entry for `typo3_documentation_lookup` named a source, "Needing
  the official API, reference or tutorial documentation". A session with a
  behaviour question does not recognise that as its own.
- The same session reported the same shape twice more.
  `feedback/2026-07-31-174526` ends "No lookup covers whether such a
  registration is still consumed in the active version: I had to read installed
  vendor core". `feedback/2026-07-31-174529` names the pair "per-version
  behavior questions".

## Decided

- The judgement is **step 3 of the ladder**, routing, not the absent capability
  the feedback proposes. The tool that answers exists and answered in one call;
  nothing pointed the question shape at it.
- The routing half is **closed on the spot**. The false clause is out of the
  changelog entry and an entry for the shape now names
  `typo3_documentation_lookup` with `targetVersion`.
  [judging.md](../../documentation/records/judging.rst) puts that on the
  autonomous side. It touches no `src/`, no declared schema and no skill
  contract, and it writes no statement about TYPO3.
- The skill half is **queued**, because the routing block reaches a session only
  through `typo3_server_scope`. The order this session followed is
  `skills/base.md`, where the changelog sweep is a numbered step and
  `typo3_documentation_lookup` is a conditional bullet under it. That is a skill
  contract, so a review makes the change rather than an improvisation.
- Recorded here rather than against `typo3_changelog_lookup`, because the
  property belongs to every lookup over a record of events. A caller asks the
  changelog what happened; a caller asks the reference for the version whether
  something holds today.

## Assumed

- That the manual answers this class of question at the covered versions. The
  evidence verifies one case. Nobody has verified the second instance, the inert
  `contentRenderingTemplates` registration; a magic key nothing consumes may
  well have no page anywhere.
- That a routing entry in the form of a question reaches a caller that an entry
  in the form of a source did not. Nothing measures which of the two a session
  matches itself against.

## Wrong if

- ~~A session follows the new routing entry, calls `typo3_documentation_lookup`
  at the target version, and still has to read the installed core by hand. Then
  the manual is not the answer for this shape. The feedback's own proposal, a
  capability that resolves behaviour rather than change, was the gap after
  all.~~ Fired in half on 2026-08-03. The subject narrowed instead. The manual
  answers a documented surface, and for a PHP identifier the routing terminates
  at the class. The capability stays unbuilt.
- ~~The skill half lands and a later conformance review reports the same "I had
  to read installed vendor core" end. Then the order was not what kept the tool
  from a call.~~ Fired on 2026-08-03 from a core patch review, which quoted the
  sentence back. It showed where the sentence stands rather than what it says:
  the miss is where the caller stands when the changelog comes back empty.
- ~~A feedback disputes the changelog's silence the other way, with an entry
  that exists and the query did not reach. That would make this a matcher
  problem rather than a routing one.~~ Fired on 2026-08-02 and again on
  2026-08-03. The matcher was sound both times and the query shape was not,
  which is `D-SKL-003`. The miss is what tells a genuine silence from one the
  caller asked for.
- A session has both, the statement in reach and the identifier named in the
  changelog. It still has no way to tell whether the thing exists in the version
  it runs on. That is what would trigger the capability the first bullet
  reserves. Written on 2026-08-03, from the third read below.

## Since then

The skill half landed as a sentence on the sweep rather than as a step of its
own, and two things settled that. The sweep can state its query set before it
opens a file, while a version-behaviour question has none until the read raises
it. So a sixth step would be a call every task pays for with nothing to put in
it. And the failure was not an unasked question but an empty changelog read as
its answer, which belongs on the step that produced the silence. The base
carries it, which reaches every published skill through the copy the installer
writes.

## Since then

The second and the third **Wrong if** fired together in one report, and neither
answer is the one they name. A core patch review ends the same way an audit did,
with an empty changelog and then grep over the installed core. The session
quotes the order back: it had read the routing and never called it. So the order
reached it and did not fire at the moment the silence arrived. That says where
the sentence stands rather than what it says.

The third is the sharper half: the silence was not genuine. The changelog
carries the entry that answers the load-bearing question of that review, and the
reported query returns it.

## Since then

The first **Wrong if** fired in half, and the corpus rather than the routing
decides the half. A session followed the routing and read the class by hand
anyway. Re-run, the two shapes come apart. A ViewHelper's name returns the
reference page that carries the answer whole. Three forms of a PHP identifier
return index and reference pages that name the method nowhere.

The tool's own header says why. It matches page titles and section paths, never
the text of a page, and no page has a PHP identifier as its title. So the entry
stands and its subject narrows to a documented surface. For an identifier the
routing terminates nowhere, and the step after it is the class.

## Since then

Two reads built what the read above queued and established nothing beyond it.
Step 4 went into the base rather than the conformance skill. What it says at the
point of the call is which corpus a question has. A documented surface goes to
the manual, an identifier to the changelog under its own name and then to the
class. The skill defers to the base for why the changelog cannot answer. So a
bound written there would leave the sentence it bounds unqualified in every
published copy.

Step 1a settled what makes a read of the annotation what it is.
