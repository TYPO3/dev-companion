---
id: D-VER-009
title: A deprecation's migration target is asked for by its own issue number
date: 2026-08-21
status: open
---

# D-VER-009 — A deprecation's migration target is asked for by its own issue number

**The version a deprecation's replacement arrived in is one
`typo3_changelog_lookup` call on that deprecation's own issue number.**

A package that declares two majors asks the same question of every entry a sweep
returns: is the replacement on the lower one. The call that answers it was here
for weeks while the session that needed it settled each entry by hand.

## Evidence

- `feedback/2026-08-19-094221`. `t3g/blog` declares
  `^13.4.15 || ^14.3 || 15.*@dev` with 14.3.6 installed. The session ran the
  sweep and got five deprecations. It established each migration target's
  version from a listing of the installed changelog directories one version at a
  time. It reports that the one lookup it eventually guessed, a query with no
  version filter, "cost one round trip and replaced the whole procedure". It
  expected the route not to generalise because "the feature happens to be titled
  after the words I picked".
- The issue number is a query, and it reaches every sibling. Measured on
  2026-08-21 over the 3768 entries of `.checkouts/14.3`: `108557` returns
  `14.2/Deprecation-108557`, `14.2/Feature-108557` and `14.2/Important-108557`,
  each with its version. The entry key is the file name and the number is a word
  of it. So this is the ordinary name pass and no body read.
- The identifier generalises further than the report expected.
  `allowedRecordTypes` returns the Deprecation and the Feature of 14.2 in that
  same name pass, without `D-ANS-042`'s body fallback. `Namespaces.php` reaches
  nothing, which is the case the number covers. A path is not an identifier, and
  the siblings of #108524 are still one call away.
- The core writes the pair under one issue often enough to matter and not often
  enough to be the whole answer. Of the 75 deprecations in `14.0` through
  `14.3`, 28 have a sibling entry under the same number and 24 of those a
  Feature. All five the report names are among the 28.
- What the other 47 need already exists.
  `extension/compatibility/a-declared-major-that-is-not-installed`, written
  2026-08-18, says three things. An entry that places an API before the older
  declared major settles that it is there. Nothing writes an entry for what did
  not change. The branch closes what the changelog narrows.
- Nothing put any of the three where the sweep passes. `skills/base.md` step 5
  sends an identifier to `typo3_changelog_lookup`, written 2026-08-03, under the
  heading of "does this still work". That is two sentences after the manual's
  page-title limit, which is the sentence the report quotes as its reason to
  leave that route. The `query` parameter says the match reads the file name and
  never that the issue number is a word of it. The `routing` entry onto the
  compatibility page fires on *writing* against a core API, and a sweep reads.

## Decided

- **Step 2 of the ladder, delivery, and queued.** All three routes predate the
  feedback by two weeks or more and none reached a dual-major audit. Nothing is
  absent from the corpus, so what is due is placement and words.
- **Rejected: the `declaredVersions` parameter and the
  typo3_migration_availability tool the report asks for.** Both would have the
  server read a Migration section, take the replacement identifier out of prose
  and answer with a version. The caller already holds that answer as a query.
  What earns a tool is the round trips it takes off the caller (`D-FBK-027`),
  and this one is at one.
- **The number is what the words name, not the identifier.** An identifier
  reaches the pair where the core spelled it, and the report is right that it
  does not always. The number reaches the siblings whatever the entries' titles,
  because it is in every one of their file names.
- **The boundary is the sibling.** Inside: the version a replacement announced
  under the deprecation's own number arrived in. Outside, and `D-VER-007`'s
  page: a replacement older than the deprecation, or one that was never a change
  event, where the branch is what answers.
- **Priority `normal`.** This sweep filed two feedback and the three behind
  `D-VER-007` and `D-VER-008` describe the same dual-major situation from other
  task shapes, which is not `low`. One repository's session series is not
  `high`.

## Assumed

- That 14 is representative. The 28 in 75 came from one major. Nobody has
  measured whether the core paired entries this way on 13, or will on 15.
- That a caller with a deprecation in hand holds its issue number. Every entry
  this tool returns carries `issue`, so the assumption is that the caller reads
  its own previous answer.

## Wrong if

- A session asks for the number and reads the sibling Feature's version as the
  version the replacement is usable *from*. The pair lands together, so the
  number says when the replacement arrived and never that the deprecated call
  still works beside it.
- A feedback reports the sweep still costs a directory listing per entry after
  the words land. That would put the failure in the routing rather than in the
  sentence.
- The count on another major comes out far below 28 in 75. That would make the
  sibling a coincidence of 14 and the branch read the ordinary answer.
- A short number reaches an entry it sits inside. One of the 3281 distinct
  numbers in `.checkouts/14.3` does today. `7098` returns
  `7.5/Breaking-67098-CorrectRequired-parameterInTextfieldViewHelper`, because
  the key matches by containment. Every number the core issues now is six
  digits, so what nobody has measured is whether that stays the only one.

## Since then

The words landed in `skills/base.md`'s deprecation sweep, with the compatibility
document named for the case where no sibling exists. What it does not carry is
the caveat the first **Wrong if** names, that the pair lands together. A session
re-measured two of the four on 2026-08-23 and neither fired. The sibling is not
a coincidence of 14, and the lower majors pair at a higher rate. The
short-number collision is still exactly one on every covered branch.
