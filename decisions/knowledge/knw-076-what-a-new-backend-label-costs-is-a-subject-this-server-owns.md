---
id: D-KNW-076
title: 'What a new backend label costs is a subject this server owns'
date: 2026-08-14
status: confirmed
---

# D-KNW-076 — What a new backend label costs is a subject this server owns

**How a new XLF trans-unit reaches the backend's `~labels/` module is inside
this server's boundary and absent from it. The feedback goes to the queue rather
than closes.**

A session that added labels to a backend TypeScript module got a green build and
a runtime throw. The types regenerate, the editor autocompletes, lint passes. It
read the two producers out of the checkout itself.

## Evidence

- The feedback's own query still reaches only what it reported. Run in this
  checkout on 2026-08-14,
  `bin/cli hints:probe "JavaScript labels module cache flush after adding XLF trans-unit"`
  classifies it `typescript, xliff`. It matches `language-files`,
  `backend-typescript` and `backend-ui`.
- None of the three answers it. `language-files` in `labels.json` is for an
  author, the locale prefix, the `target-language` trap, the XLIFF linter, and
  names no JavaScript. `backend-typescript` in `backend-ui.json` states the twin
  failure for the other producer. "The generated file is committed, so a source
  change that is not built leaves the two disagreeing and the browser running
  the old one."
- `~labels` occurs once in the whole corpus, in `testing.json`, and it is the
  functional test middleware that returns `{ get: key => key }`. Nothing says
  where the served module comes from.
- The mechanism is real and younger than the covered lines.
  `JavaScriptLanguageDomainProvider` is absent from `.checkouts/12.4` and
  `13.4`, and present in `14.3` and `main` as
  `core/Classes/Localization/JavaScriptLanguageDomainProvider.php`,
  `backend/Classes/Controller/JavaScriptLanguageDomainController.php` and an
  install middleware of its own. `D-KNW-067` read the same boundary from the
  test side.
- The symptom string is the one somebody searches with.
  `Build/Sources/TypeScript/backend/localization/label-provider.ts:55` on `main`
  throws `'Label is not defined: ' + String(key)`.
- The two producers are genuinely different things.
  `Build/lib/generate-label-types.js`, which `Build/Gruntfile.js` and
  `Build/tsconfig.json` name, writes the type stub.
  `createLanguageDomainResponse()` builds the module the browser runs from
  `LanguageService::getLabelsFromResource()`. It sends it with
  `$lifetime = 3600 * 24 * 365`, as `Cache-Control: private, max-age=` and an
  `Expires` that matches.
- The label module is exempt from the cache bust every other module gets, and
  carries an infix of its own instead. `ImportMap::resolvePaths()` on `main`
  skips a `VIRTUAL:` address before it adds the `?bust=` suffix.
  `JavaScriptLabelImportMapEntryResolver` builds the `language_domain` route
  with a `cacheBustInfix` from `package-dependent-cache-identifier` hashed with
  `JavaScriptLanguageDomain`.
- This run left open on purpose whether that infix moves when a trans-unit
  arrives, and it is what decides half of the report.

## Decided

- Step 1a of the ladder, and queued rather than closed on the spot. What lands
  is a statement about TYPO3 read on both sides of a version boundary. This run
  has read this repository and two core checkouts.
- Bound `since: 14`. The provider exists on `14.3` and `main` and on neither
  covered LTS. So an unbound sentence would describe a mechanism half the
  callers do not have.
- Not step 2, so nothing moves. `backend-typescript`'s sentence about stale
  generated JavaScript is about a build artefact in the repository. The label
  module is an HTTP response with a year on it. The one sentence carried to the
  other case would state the wrong repair.
- The feedback's **Suggestion** is not copied down. It asks for `cache:flush`
  and a hard reload as two steps that are both needed, and the `cacheBustInfix`
  is a mechanism it does not mention.
- Inside the boundary. This is the core's own backend asset pipeline, which
  `backend-typescript` already answers for at scope `core`.

## Assumed

- That the session's account of its own installation holds: the `.d.ts`
  regenerated while the module the browser ran did not. Nothing here started a
  build or a browser.
- That the answer is one statement rather than two. The server-side invalidation
  and the browser-side one may turn out to be a single step.

## Wrong if

- The `cacheBustInfix` moves when a label file changes. The browser then asks
  for a URL it has never seen, nobody needs a hard reload, and what remains is
  the server-side half alone.
- `cache.l10n` keys the parsed file on its `filemtime()`. Then nobody needs a
  flush either and the statement is a correction rather than a procedure. That
  is what `D-KNW-027` found when the same question came up for the Fluid
  template cache.
- The statement lands on `language-files`. That hint is for an author, comes
  from `xliff`, and holds on every covered major. This holds from 14 and comes
  from a TypeScript task.

## Confirmed on 2026-08-14

The gap is filled. Neither **Wrong if** fired, and that is the finding: both of
the report's steps are owed. The parsed-label cache keys on nothing that changes
when a unit arrives. The asset identifier composes from the version, the project
path and the package identifier. So no label file reaches either, and the URL
under a year of `max-age` stays the one the page fetched. That is where this
reading differs from `D-KNW-027`, whose cache keyed on the file's modification
time.

Both **Assumed** resolved, and the answer is one statement per half because
different people clear the two caches. Two findings the entry did not
anticipate. The mechanism is not core-only, and the label module is the one
asset whose invalidation is a route parameter.
