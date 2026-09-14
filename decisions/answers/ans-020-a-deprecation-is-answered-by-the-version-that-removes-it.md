---
id: D-ANS-020
title: A deprecation is answered by the version that removes it
date: 2026-08-02
status: open
coveredBy:
  - PackageSourcesTest::aDeprecationSaysWhichVersionItStopsWorkingIn
  - PackageSourcesTest::aRemovalClauseThatIsNotThisEntrysIsNotReadAsOne
  - PackageSourcesTest::whereTheEntryStatesNoRemovalTheRuleTravelsWithTheAnswer
---

# D-ANS-020 — A deprecation is answered by the version that removes it

**An upgrade audit decides on the removal version. The changelog answer carries
neither it nor the rule that would supply it where an entry is silent.**

`typo3_changelog_lookup` names the entry, its tags and the file to read. What it
does not say is when the deprecated thing stops, which is the fact a "is this
future-proof" review turns on. Neither source is whole on its own. The entries
state a removal in prose for 44 of the 75 deprecations of one major. The rule
that would cover the rest has an exception inside the same corpus.

## Evidence

- `feedback/2026-07-31-194821` re-run on 2026-08-02 through
  `ChangelogLookup::answer()` against `.checkouts/14.3`. `query: "yaml"` with
  `version: "14.2"` returns the two `#109412` entries, each with its type,
  version, issue, title, tags and `EXT:core/…` path. Neither the rendered text
  nor the `entries` data carries a removal version. The behaviour has not
  changed since the feedback's date.
- The fact is in the file the answer points at, as prose in the Description
  section. `14.2/Deprecation-109412-FormYamlConfigurationRegistration.rst` reads
  "The TypoScript-based paths will still be loaded during the deprecation period
  but will be removed in TYPO3 v15.0". That is the read the session reports it
  did by hand.
- Parsing it back out reaches half the corpus. Of the 75 deprecations
  `.checkouts/14.3` files under 14.0 to 14.3, 44 state a removal version
  anywhere in their prose and 31 state none. For 13.0 to 13.4 it is 27 of 63.
  The wording is free: "will be removed in", "will be removed with", "marked for
  removal in", against "TYPO3 v15", "v15" and "v15.0".
- Where 14 states one it is v15, without exception. All 44 name v15 and no other
  release. The two that also carry another number carry it out of a `:ref:`
  anchor in a different sentence, not out of a removal clause.
- The rule that would answer the other 31 is here and does not travel with the
  answer. `bin/cli hints:probe` reaches `deprecated-apis` in
  `knowledge/architecture-hints/general.json` from any query with "deprecat" in
  it. It states "A deprecated API keeps working until the next major release".
  `typo3_changelog_lookup` returns no hints at all, and its last paragraph names
  the migration and the Extension Scanner and stops.
- For this entry the corpus is more specific than that, and a reader on 14 gets
  none of it. The `form-framework` hint states that the
  `plugin.tx_form.settings.yamlConfigurations` registration is "deprecated for
  removal in the next major", bound `until: 13`. So the filter drops it at the
  version the reviewer audited. `ArchitectureLookup` at `version: 14` with
  `form yaml registration deprecated` matched no hint whatsoever and fell back
  to listing the requestable ids.
- The rule alone would be wrong at least once in this corpus.
  `13.4/Deprecation-105297-DeprecateTableoptionsAndCollateConnectionConfiguration.rst`
  names "TYPO3 v15 (or later)" and skips v14, and it held. On `.checkouts/14.3`
  `ConnectionPool::migrateTableOptionsToDefaultTableOptions()` still migrates
  the key, under an
  `@deprecated since 13.4 and will be removed in v15 (or later as it does not hurt to keep them)`.
  So "the next major" is the default and the entry is what carries the
  exception.

## Decided

- **Step 2 of the ladder, delivery.** The statement exists in `knowledge/`, in
  two wordings. It reaches only a tool the session had no reason to call while
  it read a changelog answer. Not 1a — nothing about the removal of a 14
  deprecation is unknown here. Not 1b: no verb is absent, `lookup` is the verb
  and it is the one the session called.
- **Queued rather than closed on the spot.** The lever is
  `ChangelogLookup::answer()` and probably its declared `outputSchema`, and what
  the changelog states about removal needed `.checkouts/` to establish. Both are
  what [judging.md](../../documentation/records/judging.rst) puts beyond a run
  that has read only this repository.
- **The feedback's suggestion is right about the fact and understates the
  shape.** It asks for the removal version "for deprecation entries", which
  reads as a field parsed per entry. That field is empty for 31 of 75 entries of
  one major. A caller reads an empty field beside a full one as "no removal
  planned", which is the silence-as-verdict failure `D-ANS-009` already stands
  against.
- **Which shape closes it is not settled here.** A parsed clause, a stated rule
  in the last paragraph, or both with the entry over the rule are three answers.
  The choice between them is research the todo owns. The `@deprecated`
  annotation at the trigger site is a fourth source and is more exact than the
  prose in the one case checked above.
- Recorded against a new entry rather than against
  [`D-SKL-003`](../task-skills/skl-003-a-sweep-is-bounded-by-the-changelogs-own-axes.md)
  or [`D-ANS-016`](ans-016-a-miss-names-the-query-that-would-have-hit.md). Those
  two are about the way to the entry: the bounds a sweep carries, and what a
  miss owes the caller. This one is about what the entry says once the caller
  has it, and the call that produced it hit.

## Assumed

- That an audit needs the release and not only the order. The session says so
  itself: it had the deprecation in hand, knew of the deprecation, and still
  opened the file. Nothing here can check that a version number rather than "the
  next major" was what it went for.
- That the 14 corpus is representative of how the core states a removal. The
  count covers `.checkouts/14.3` and its 13 directories, not 12 and older, and
  the conventions for the text have moved before.

## Wrong if

- The same finding follows a changelog answer that states the removal version.
  The fact would then arrive and the session would not take it, which is step 4
  and a rewrite rather than a placement.
- A reviewer acts on a stated removal version that the entry contradicts. That
  is a 14 deprecation the core keeps past 15.0, in the shape `#105297` already
  has on the branch below. Then the answer asserted the rule where only the
  entry could speak.
- The count moves under this. A later `.checkouts/` update files 14 deprecations
  that name a removal other than v15. Then "always v15" was a snapshot rather
  than a policy, and the parsed clause is the only honest source.

## Since then

Step 2 landed and the open question was the shape. It is the clause parsed out
of the entry **and** the rule stated once, with the entry over the rule. Every
entry carries the field, empty where it states none, and an answer with a
deprecation carries the rule beside it. Both stand in the data as well as in the
text, because `R-ANS-002` stands against a client that renders the payload and
drops the text.

The rule does not become a per-entry number, and that is the whole difference
from a fourth candidate nobody proposed. A number derived from "the next major"
would contradict an entry the core kept. That is the second **Wrong if**, in the
field a caller acts on.
