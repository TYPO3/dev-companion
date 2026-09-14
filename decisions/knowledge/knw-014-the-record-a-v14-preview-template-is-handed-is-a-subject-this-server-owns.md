---
id: D-KNW-014
title: 'The record a v14 preview template is handed is a subject this server owns'
date: 2026-08-02
status: revoked
revokedBy: D-KNW-020
---

# D-KNW-014 — The record a v14 preview template is handed is a subject this server owns

**What a TYPO3 14 backend preview template can read off `{record}` is inside
this server's boundary and absent from it. So the feedback goes to the queue.**

The corpus says where a preview template registers and stops there. A session
that follows it arrives at a template with one variable in it. Nothing here says
what that variable is or what comes back from a field on it.

## Evidence

- The miss reproduces. Called over stdio with the feedback's own query and
  `targetVersion: "14"`, `typo3_hint_lookup` returns one hint,
  `fluid-templates`. No statement in it names a record variable.
  `bin/cli hints:probe` on the same query reaches the same single hint.
- Nothing below `knowledge/` or `skills/` contains `RecordInterface`, `{record.`
  or `LazyRecordCollection`. The entry the subject belongs to is
  `content-elements`, whose one preview statement gives the page TSconfig key
  `mod.web_layout.tt_content.preview.<CType>` and the TypoScript beside it. It
  carries no version range, so it also cannot say that v14 changed what the
  template gets.
- The feedback's claims about TYPO3 hold. `.checkouts/14.3` has
  `Documentation/Changelog/14.0/Breaking-92434-UseRecordAPIInPageModulePreviewRendering.rst`,
  and `Core\Domain\Record` declares only `get()` and `has()`, inherited from
  `Psr\Container\ContainerInterface` through `RecordInterface` — no `__get`, no
  `ArrayAccess`.
- The version boundary is readable in the checkouts, which is what a statement
  needs to carry a `since`. `FluidBasedContentPreviewRenderer` calls
  `assignMultiple($row)` and then assigns `record` on 13.4; on 14.3 it assigns
  `record` alone.
- The mechanism the session could not find is readable too, and not from this
  repository. In typo3fluid/fluid 5.3.1, the release 14.3 requires,
  `StandardVariableProvider::getByPath()` resolves a path segment on a
  `ContainerInterface` through `has()` and `get()`, ahead of every getter it
  tries. Read at that version in a local installation's `vendor/`, because no
  core checkout has one.
- The relational half has its ground in the same checkout rather than in an
  installation, so this goes to the queue and not to `waiting/`.
  `Core\DataHandling\RecordFieldTransformer` is where a field value becomes what
  the template sees, and `theme_camino` ships preview templates that iterate a
  relation with `f:for` — `ContentPreviews/Linklist.fluid.html`. Which field
  types reach that branch is the todo's research, not this one's.

## Decided

- Step 1a of the ladder, and queued. Content elements are work this server
  answers for, the statement binds to a major, and there is nothing here to
  reword or move.
- Not step 3. The route works where the query carries the corpus's own form:
  `tt_content preview template` reaches `content-elements` at
  `appliesTo(10) + text(129)`. What the caller's own form does to the same query
  is
  [`D-ANS-022`](../answers/ans-022-the-matcher-takes-a-hyphenated-compound-apart-measured-over-the-corpus-first.md).
- Not closed on the spot. Every statement needs a read on 13.4 and 14.3 and
  against the Fluid release each of them pins. This run has read nothing but the
  greps above.

## Assumed

- The statement belongs on `content-elements` rather than on `fluid-templates`.
  What a preview template gets follows from the CType it previews, and a session
  that writes one arrives there from the registration.

## Wrong if

- What the todo establishes is Breaking-92434 restated. The entry is then a
  pointer to `typo3_changelog_lookup` on `content-elements` rather than a
  statement of its own.
- What a field resolves to turns out to depend on the TCA of that field rather
  than on the Record API. So no single statement holds for "a relation in a
  preview". The hint then has to name which field types resolve to records and
  which stay a string, or say nothing.

## Confirmed on 2026-08-02

The gap was real and the statements landed. The first **Wrong if** did not
happen. The changelog says which variables changed and neither how a path
resolves nor what a field comes back as. The second did, in the form the entry
named. The type the schema built from its TCA chooses the branch a field takes.
So the hint names the five that come back as records and says which case stays
values. Two of the statements are older than the major the feedback is about,
which the checkouts settled rather than the changelog.

## Since then

A second gap on the same statement got its judgement on 2026-08-02 and sits in
the queue beside this one. What a Fluid preview template *replaces* is the
content half of the preview only, and the header the standard renderer draws
stays above it,
[`D-KNW-015`](knw-015-the-corpus-states-what-a-fluid-preview-template-replaces.md).
Both todos rewrite the one preview statement on the `content-elements` hint, so
whichever lands second rewrites in place rather than adds beside. The two
statements differ in what binds them. What `{record}` is moved in 14 and needs a
`since`, while the header/content split reads the same on 13.4 and 14.3.

## Revoked on 2026-08-02

By the work this entry queued: its headline said the fact was absent from this
server, and it landed the same day. An entry a reader may build on has to be one
whose headline is true when they read it. The account above stays where it is,
and `D-KNW-020` holds from here. This entry's **Wrong if** were both about what
the todo would find, so nobody could go back to either once it had found it.
