---
id: D-KNW-053
title: 'The root-package layout is stated from an installation'
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::installingTypo3BeneathTheExtensionNamesTheInertKey
---

# D-KNW-053 — The root-package layout is stated from an installation

**A session read the three statements off a root package built for the purpose.
All three are one sentence each rather than one per installer major.**

The report's pair was TYPO3 14.3.5 with `typo3/cms-composer-installers` v5. What
decided the bound is that every covered major requires that installer major. So
the hint carries no `since` and no `until`.

## Evidence

- Built rather than reasoned about. A root `composer.json` of type
  `typo3-cms-extension` that requires `typo3/cms-core` resolved and installed
  against the network on 2026-08-03. It set `config.vendor-dir`,
  `config.bin-dir` and `extra.typo3/cms.web-dir` below `.build/` and
  `extra.typo3/cms.app-dir` to `.build`. `D-KNW-047` assumed nobody could do
  that here.
- The message is unconditional on `web-dir`. A second root package with
  `app-dir` alone printed the same line. `Plugin/Config.php` raises both it and
  the `root-dir` one, from the presence of the key and before anything resolves.
- One installer major across the range. `typo3/sysext/core/composer.json`
  requires `typo3/cms-composer-installers` at `^5.0` on `.checkouts/12.4` and
  `^5.0.2` on `13.4`, `14.3` and `main`, and all four environments below
  `.environments/` carry v5.0.2.
- The installer drops `app-dir` rather than applies it. The generated
  `vendor/typo3/autoload-include.php` sets `TYPO3_PATH_APP` to the Composer root
  and `TYPO3_PATH_ROOT` to the `web-dir`. `typo3 setup` then wrote
  `config/system/settings.php` and `var/` into the repository, while
  `fileadmin/` and `typo3temp/` went to `.build/public/`.
- `web-dir` has a boundary of its own. Set to `../outside-public`, the installer
  resets it to the default with «TYPO3 public path must be a subdirectory of
  Composer root directory». That is the same guard and worth the clause.
- The `cms-cli` constraint is the core's on every covered major: `^3.1` on
  `12.4`, `^3.1.1` on `13.4`, `^3.1.3` on `14.3` and `main`. A root require of
  `^5.0` reproduced the reported failure verbatim, naming `typo3/cms-core` as
  the conflicting party.
- The placement is readable in the artifact. `my_extension` is an active package
  in `vendor/typo3/PackageArtifact.php` with a `packagePath` of `""` relative to
  the project path. Its `_assets/<hash>/` symlink points at
  `../../../Resources/Public`, above the document root and into the repository.

## Decided

- One hint of its own rather than a clause in `project-build-and-scripts`. That
  hint is `scope: project` and about a repository that holds an installation.
  This is the other unit, so it joins `extension-repository-layout` and its two
  neighbours as a fourth.
- Unbound. Nothing in the three reads differs across the covered majors, and a
  `since` would claim a boundary nobody found.
- The `typo3conf/ext/` half reads "empty where it exists at all". The directory
  is absent on `13.4`, `14.3` and `main` and present and empty on `12.4`, and
  both read to a session as the same suspicion.
- The hint carries the clause that `web-dir` has to sit below the Composer root,
  though nothing asked for it. It is the failure the same session meets one line
  after it moves the installation.

## Assumed

- That the message stays a property of the installer rather than of the core.
  This run read both at one pair of versions, and only the installer's source
  raises it.

## Wrong if

- A covered major stops its requirement of installer major 5. The `app-dir` and
  `web-dir` statements would then need a bound, and nothing in the hint says
  which read came from which side.
- `typo3/cms-cli` releases a major the core requires. The sentence rests on the
  fact that the constraint is the core's rather than on any number. But "mirrors
  the TYPO3 major" would no longer be the shape of the mistake.
- A session that composes the layout reads the statement and still requires
  `typo3/cms-cli`. The gap would be in the routing rather than in the corpus —
  carried over from `D-KNW-047`.
