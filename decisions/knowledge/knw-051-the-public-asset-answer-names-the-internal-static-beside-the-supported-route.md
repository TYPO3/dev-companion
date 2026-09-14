---
id: D-KNW-051
title: The public-asset answer names the internal static beside the supported route
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::thePublicAssetAnswerSeparatesTheSupportedRoute
---

# D-KNW-051 — The public-asset answer names the internal static beside the supported route

**Where a class holds a supported entry point beside a deprecated and an
`@internal` one, the corpus names all three and says which is which.**

`public-assets` named the supported route and the deprecated predecessor, and
left `PathUtility::getSystemResourceUri()` to whoever opened the class. The
session that reported opened it, read the signature, and cleared the call as
current API. One line above the signature the docblock reads
`@internal Will be removed (or made private) before v14 LTS release`.

## Evidence

- The query re-run against the server as it is now, `typo3_hint_lookup` with the
  feedback's own arguments at `targetVersion: 14.3`. `public-assets` returns the
  factory-and-publisher route `[TYPO3 v14 and newer]`, and directly under it
  "PathUtility::getPublicResourceWebPath() is what computes such a URL here.
  `[up to TYPO3 v14]`". That is a method deprecated on that very version,
  presented as what computes the URL, with no word of the deprecation.
  `getSystemResourceUri` occurred nowhere in the answer.
- The TYPO3 claim, read in `.checkouts/14.3` and `.checkouts/main`.
  `PathUtility::getSystemResourceUri()` is at `Classes/Utility/PathUtility.php`
  line 135 on 14.3 and line 93 on `main`. It carries the same docblock on both:
  `@internal Will be removed (or made private) before v14 LTS release`. It is
  four lines of body that call the factory and the publisher.
- The feedback's premise fails on the half that decides it. It reports the
  method as "current in the installed core" and cites the signature; the
  docblock one line above says the opposite. So the hint that lists one route is
  correct, and the finding the session feared as a false positive is a true one.
  That is extension code that calls a static the core has reserved for its own
  migration.
- Its suggestion would have written that method into `knowledge/` as a supported
  shorthand. That is the failure `judging.md` names, a feedback's own guess
  about TYPO3 copied into the corpus.
- `knowledge/versions.json` has 14 at branch `14.3`, status `stable`. The
  removal the docblock announces is ahead rather than behind, which is what
  makes the finding current rather than historical.
- The core deprecates the sibling in the version the hint answered for.
  `getPublicResourceWebPath()` at line 112 opens on
  `trigger_error(..., E_USER_DEPRECATED)`.
  `Deprecation-107537-getPublicResourcesWebPath.rst` in the 14.0 changelog says
  it is "deprecated first, before being removed with TYPO3 v15.0", and it is
  absent from `main`. The statement's `until: 14` band therefore rendered it as
  current advice on the one version where it warns.
- The same changelog is evidence for the read rather than against it. It says
  `getPublicResourceWebPath` "was marked internal since its introduction" and
  the core deprecated it anyway because "there were no good alternatives". The
  core's own account of an internal-marked static on this class is that it was
  never the answer.
- That changelog's migration example imports `SystemResourceFactory`,
  `SystemResourcePublisherInterface` and `UriGenerationOptions` from
  `TYPO3\CMS\Core\Resource`. None of the three is there:
  `Classes/Resource/SystemResourceFactory.php` does not exist, and the classes
  are under `Classes/SystemResource/` and `Classes/SystemResource/Publishing/`.
  A session that follows the only published migration gets three imports that do
  not resolve.
- Step 4, and not 1, 2 or 3. `bin/cli hints:probe` on the feedback's query
  reaches `public-assets` as the PHP hit, `appliesTo(17) + text(41)`. The server
  delivered it, and the feedback quotes it back. What it says is what did not
  take.
- The same audit counts the cost twice. The sibling `feedback/2026-08-03-164805`
  lists this method among the identifiers it had to settle by a grep of
  installed source. It repeats the same "current" read from the same signature.
  Both sessions stopped one line short.

## Decided

- `public-assets` gains two `since: 14` statements. One says that
  `getPublicResourceWebPath()` still resolves the path and is the way out rather
  than the way in. It raises `E_USER_DEPRECATED` per call and is gone in the
  next major. The other says that `getSystemResourceUri()` is a third thing
  beside the two: `@internal`, reserved for the core's own call sites, a finding
  rather than a shorthand.
- Both carry no version number, no changelog id and no count, which is what
  `HintsTest::noHintStatesSomethingThatOnlyHoldsOnOneBranch` holds a hint to.
  The band carries the branch and the sentence has to read the same on all of
  them. "The next major has removed it" and "before this major reaches LTS" are
  what that leaves. They are the reason a writer could not simply put the
  deprecation into the sentence that was already there.
- The `getPublicResourceWebPath()` statement moves from `until: 14` to
  `until: 13`. On 13 it is what computes such a URL; on 14 the sentence above
  replaces it. A hint that names a deprecated method as current on the version
  it warns on is the defect, not the wording around it.
- The hint names the internal method rather than merely leaves it out —
  [`D-KNW-011`](knw-011-a-rule-that-names-a-defect-names-its-correction.md).
  A session arrives at this hint after it found the method in the class. A hint
  that is silent about it leaves what the class suggested in place. That is the
  shape of
  [`D-KNW-043`](knw-043-a-rule-carries-the-strength-of-its-claim-and-its-source.md)
  read from the other side. There the corpus flattened two neighbour APIs into
  one strength, here it stated one and said nothing about the third.
- This entry inverts the general rule the feedback proposed rather than adopts
  it. It asked that a hint state every supported alternative. This case shows
  that the reader's ambiguity is not *which of two supported routes*. It is
  *this one compiles and works, am I allowed to call it*. So what a hint owes is
  the entry points that are **not** for a caller, with the mark they carry.
- The hint names the three classes the route injects with their namespace,
  against the migration example that imports them from one they are not in.
- `appliesTo` gains `PathUtility`, `getSystemResourceUri` and
  `getPublicResourceWebPath` — `R-KNW-002`, the subject asked about in the words
  of the API. The second of those reached this hint from no query before.
- This commit archives the feedback and deletes its card. Nothing here needed a
  lookup beyond the checkouts the ladder already owes a feedback that makes a
  claim about TYPO3. It touches no schema, tool or skill contract.
- Not decided here: whether a call to `@internal` core API should be a named
  finding class in `typo3-extension-conformance`. `@internal` occurs in no skill
  under `skills/`, so nothing explains the mark to the reviewer today. But that
  orders a task rather than states a fact about TYPO3, and no feedback asks for
  it. It stays for the card that reaches the skill.
- Not folded in either: the identifier lookup `feedback/2026-08-03-164805`
  proposes. It would have surfaced this docblock in one call, and it is a
  capability rather than a wording fix; its own card carries it.

## Assumed

- That `@internal` on a public static is what settles whether extension code may
  call it. The docblock and the 107537 changelog are what this rests on, not a
  policy statement of the core's read for this judgement.
- That a session which found the method in the class reads the corpus again
  before it decides. Nothing measured that. What this entry knows is that this
  session went to the class *because* the hint accounted for neither of the two
  statics it found there.
- That the audit target of the session that reported really calls the static as
  quoted. The extension is in another checkout and this run did not read it. The
  lever is the same either way.

## Wrong if

- v14 LTS ships with `getSystemResourceUri()` public and the `@internal` gone.
  The statement then describes a boundary that closed, and an extension that
  calls it was never a finding. The hint owes a `since`/`until` rather than a
  correction, and the docblock's own wording makes this the likeliest failure.
- A session reads the new statements and still reports the call as conformant,
  or still goes to `PathUtility` to decide. The wording was then not the gap,
  and what remains to suspect is step 2, that an audit's question does not pass
  through this hint at all.
- A session finds another hint that names one entry point of a class that also
  holds an internal or deprecated lookalike. One correction is then a fix rather
  than a rule, and the need is a sweep of the corpus instead of a sentence here.
- The 107537 namespaces turn out to be right at some patch level, or the classes
  move. The clause about the migration example then describes a docs bug
  somebody fixed, and it goes.
