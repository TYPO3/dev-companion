---
id: D-SKL-003
title: "A sweep is bounded by the changelog's own axes"
date: 2026-08-02
status: open
coveredBy:
  - SkillTest::theDeprecationSweepRunsFromTheExtensionsSurface
---

# D-SKL-003 — A sweep is bounded by the changelog's own axes

**The version, the type and the tag the changelog itself carries bound the
deprecation sweep, never a query set derived from what the extension ships.**

`skills/base.md` step 5 prescribes that query set. The words come from the
caller's package. The tool matches them, all of them at once, against titles the
core wrote about its own code. So the sweep the order exists to guarantee
returns nothing and the silence reads as a clean bill for the next major.

## Evidence

- `feedback/2026-07-31-194459` reports that the sweep produced nothing "because
  the changelog tool could not match the queries". `feedback/2026-07-31-194819`,
  a different model in the same project on the same day, carries the queries.
  `form set yaml registration deprecated` and
  `form sets discover yaml configuration`, both empty.
- Re-run on 2026-08-02 from `/home/benji/projects/site-new` through
  `bin/typo3-dev-companion`, against TYPO3 14.3. The first query still returns
  nothing, and the per-word reach line says why. "form" reaches 63 entries,
  "deprecated" 87, "yaml" 2, and no entry carries all five.
- The same call bounded by the changelog's own axes answers it.
  `type: deprecation` with `version: 14` and no query at all returns 75 entries;
  adding `tag: ext:form` returns 6, among them
  `14.2 Deprecation: TypoScript-based form YAML registration (#109412)` — the
  entry the word queries missed, tagged `FullyScanned`.
- Neither bound is in `skills/base.md`. Step 5 names `type: deprecation` and
  then fixes the rest to "the symbols and registration shapes step 2 reported".
  `SkillTest::theDeprecationSweepRunsFromTheExtensionsSurface` asserts the
  sentence "the query set is derived from the extension's own surface" verbatim.
- The tool says both. Its description calls the `tag` field "what a sweep is
  bounded by where words are not". The `query` field says "omit to list a
  version or a type as a whole". `D-ANS-006` established that reading when it
  added the field.
- A test of the feedback's own proposal shows it does not hold.
  `typo3_hint_lookup` for the sitepackage's YAML path on 14 returns the
  site-set, page-rendering and layout hints and no statement about `#109412`.
  The hints answer conventions. The tag and nothing else reached the entry the
  sweep is for.

## Decided

- **Step 4 of the ladder, wording.** The skill delivered the rule and the
  session followed it. It called the right tool with the right `type` and lost
  the sweep to the query shape the step itself prescribes. No verb is absent,
  since `D-ANS-006` shipped the enumeration and the tag, and no routing entry
  would have changed the call.
- **Queued rather than closed on the spot.** `skills/base.md` is a skill
  contract installed into somebody else's project, which
  [judging.md](../../documentation/records/judging.rst) puts on the far side of
  the autonomous line. The change also has to move `R-SKL-005` and the assertion
  above, which currently hold the wording that is wrong.
- **This entry rejects the feedback's suggestion, on the evidence above.**
  Architecture hints are not sufficient evidence for an empty sweep. What makes
  the substitution attractive is that the hints come filtered to the target
  version and carry predecessor statements. So they read like a version answer.
  What they carry is where a convention holds, and a deprecation nothing in the
  corpus describes is not in them.
- The overlap the feedback names is real and is not a licence. Both lookups
  speak about version boundaries, and only one of them is a record of what
  changed.
- Recorded here rather than against `typo3_changelog_lookup`. The tool answers
  the sweep in one call today; what is wrong is the order that asks for it.

## Assumed

- That a step naming the two bounds is enough for a session that has the
  extension's surface in hand and would otherwise type it. Nothing measures
  which wording a caller matches itself against, which is the same assumption
  `D-ANS-010` left open about routing.
- That the tags are worth a sweep bound for a project sitepackage. They name the
  system extension a change is **in**, so a sweep over a package that touches
  five of them is five calls. `D-ANS-006` already established that an extension
  key of the caller's own matches none of them.

## Wrong if

- A conformance review bounds the sweep by version and type, gets the 75 entries
  of one major back, and reports a finding for a deprecation the checkout never
  calls. Then the query set did work the step did not credit it with, and the
  fix traded a silent miss for a false positive.
- A later feedback reports the enumeration as unusable at the caller's end. Too
  many entries to verify against the checkout, at every major the package
  declares. Then the bound is a filter this server owes rather than one the
  caller composes, and `D-ANS-006`'s reading of the tag needs revisiting.
- The wording lands and the same "the sweep returned nothing" ending recurs.
  Then the query shape was not what cost it, and the base is not where the
  answer lives.

## Since then

The step names three axes and no query: the type, each declared major, and a tag
per call. The changelog itself settled which tags a sitepackage names. Every
deprecation of the three majors carries at least one tag and one per major
carries no `ext:` tag. So a sweep composed of tags leaves one entry outside
itself, which the annotations source covers. `ext:` alone is a weak bound for a
sitepackage, which is why the surface tags stand beside it. The package both
feedback swept reaches further through what it renders through than through what
it requires. The tag is also what keeps the sweep readable, since the cap cannot
return one major whole. The step lists no tags on purpose. One call with any tag
returns the vocabulary.

## Since then

A measure of the second **Assumed** in practice found the composition costs more
than the enumeration it replaces. Eleven tags, eleven calls, four that returned
nothing the session used. Against that stands one call that matches every
deprecation of that major for about half the characters. Tags dense enough to
bound a sweep with is also what makes a sweep composed of them converge on the
major. The statement stands — the axes are the changelog's own — and what
changes is the third, which `D-ANS-093` decides.
