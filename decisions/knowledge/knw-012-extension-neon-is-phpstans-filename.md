---
id: D-KNW-012
title: "`extension.neon` is PHPStan's filename"
date: 2026-08-02
status: open
---

# D-KNW-012 — `extension.neon` is PHPStan's filename

**A feedback that names `extension.neon` gets its judgement against the package
it names. PHPStan gives that filename to every extension package and TYPO3 ships
none.**

The string alone says nothing about TYPO3, and read as a TYPO3 file it points
two ways at once. At an analyser extension nobody needs, and at the
phpstan-phpunit include an extension's configuration actually has.

## Evidence

- `feedback/2026-07-31-193626`, judged on 2026-08-02. It reports a session's own
  recommendation, "Add phpstan.neon with includes: [extension.neon] from TYPO3
  testing-framework", as outdated. It asks that `typo3_hint_lookup` reflect the
  current PHPStan configuration for an extension.
- Run again from this checkout on 2026-08-02. `typo3_hint_lookup` with the
  feedback's own query,
  `extension.neon recommendation outdated - no longer needed for TYPO3 PHPStan`,
  returns `extension-static-analysis` and nothing else. It puts the
  configuration in `Build/phpstan/`, reads it off typo3/testing-framework, and
  the only include it names is the phpstan-phpunit extension. That hint landed
  in `733ed3a` on 2026-08-02, after the feedback, so what the suggestion asks
  for is there.
- `.checkouts/testing-framework/8` (8.3.3), `9` (9.6.1) and `main` (27587653)
  ship two `.neon` files each and no `extension.neon`:
  `Build/phpstan/phpstan.neon` and the baseline beside it. There is no TYPO3
  file of that name to include, on any release line the covered majors pin.
- Those same three configurations do include one:
  `../../.Build/vendor/phpstan/phpstan-phpunit/extension.neon`. The name is
  PHPStan's own convention for a package that ships rules or stubs.
  `composer/pcre` in this repository's lock declares
  `extra.phpstan.includes: ["extension.neon"]`, which
  `phpstan/extension-installer` wires up.
- The package half already had its answer on the feedback's day.
  `skills/typo3-extension-testing/references/static-quality.md`, from `b0eded4`
  on 2026-07-31, names `phpstan/phpstan` on the extension's own paths. It says
  the types come from the core's `@template` annotations rather than from a
  TYPO3-specific analyser extension.

## Decided

- The feedback is **answered** and archived by this commit. The tool it names
  answers its query correctly today, which is the whole of what it asked for.
- The hint keeps the phpstan-phpunit include. Read as "no `extension.neon` is
  needed" the feedback would delete the one include the package actually has.
  That is the correction this entry exists to refuse.
- Recorded here rather than in the hint. It is about how a claim that names a
  filename settles, against the package that owns the name. It is not about what
  an extension's configuration contains.

## Assumed

- That "an `extension.neon` for TYPO3 specifics" meant a TYPO3 analyser
  extension rather than the phpstan-phpunit include. The feedback names no
  package and the session that wrote it has ended.

## Wrong if

- A feedback reports the phpstan-phpunit include as the line an extension does
  not need, with the package read rather than recalled. Then the ambiguity above
  resolved the other way and the hint is what is wrong.
- typo3/testing-framework ships an `extension.neon` on a line a covered major
  pins. `bin/cli checkouts:update` is what would bring it in, and nothing reads
  for that filename.

## Since then

Both **Wrong if** had their read and neither has fired. No release line a
covered major pins ships the file this entry watches for. All three still
include the line the hint keeps, and the feedback's own query still reaches the
hint alone.

The first stays open and no read here can settle it. It waits for a feedback
that reports that include as the line an extension does not need. The archive
holds one report that names the file at all.
