---
id: D-DIS-004
title: The version comes from the core package, not from the console
date: 2026-07-29
status: confirmed
coveredBy:
  - InstanceTest::theTypo3VersionIsReadFromTheCorePackage
---

# D-DIS-004 — The version comes from the core package, not from the console

**The server reads the installed version from the core package's `Typo3Version`
class rather than asks `bin/typo3 --version`.**

The catalogs stand pinned to one revision and every answer read as timeless
fact. The server had the other number all along: the installation it reads for
icons and labels states its own version. Both stood known and never in contrast.

## Decided

- Read it from the core package's `Typo3Version` class rather than ask
  `bin/typo3 --version`. The version decides whether an answer holds, so it has
  to be available exactly when the console is not. A console call costs a TYPO3
  boot, on every answer that carries the catalog pin.
- Translation domains are the one answer that is withheld rather than qualified
  below a version. `13.4` has no `TranslationDomain*` class at all, `14` ships
  the mapper. So the domain string is syntactically fine and resolves to
  nothing: the label renders empty, at runtime, silently. Everything else the
  catalogs hold is markup and class names, where a qualified answer still has
  worth.

## Assumed

- The `VERSION` constant in that class stays where it is and stays a literal. It
  has stood in `Classes/Information/Typo3Version.php` on every branch this entry
  checked. An absent or unparseable one yields null, which reads as "nothing to
  compare with" rather than as a wrong version.

## Wrong if

- A 13.x patch release backports the domain API, which would make the constant
  in `Tools` wrong. It is one number in one place for that reason.
- A caller works on a version other than the installation the server found — the
  second checkout, the backport branch. The version is then read from the wrong
  place, and nothing accepts a stated one yet.

## Confirmed on 2026-08-02

The first **Wrong if** has not happened and a check guards it rather than a
promise. The two older checkouts carry no `TranslationDomain*` class and the two
newer ones do, filed as a 14.0 feature. Two readers hold the constant from here.
`catalog:check` asks every covered checkout and fails where the major it derives
is not the constant. A backport is a release rather than an edit here.
`VersionsTest` ties it to the majors `knowledge/versions.json` declares.

## Since then

The second **Wrong if** was live: this lookup asked for the major directly while
four other tools took a `targetVersion`. It closed on 2026-08-02.
`typo3_translation_domain_lookup` resolves one through `Versions::target()` like
the others, and reports the major it composed the answer for. Where nothing
states a version, it names the version domains arrive in rather than answers as
if that stood settled. It stays one major: an extension that declares two has no
single answer.
