---
id: D-COD-007
title: 'A pin goes to the newest version the declared PHP floor allows'
date: 2026-08-29
status: open
coveredBy: []
---

# D-COD-007 — A pin goes to the newest version the declared PHP floor allows

**Every version pinned here goes to the newest release this package's declared
PHP floor allows, which is the current major wherever the floor permits one.**

This is `R-COD-004` carried out the day somebody wrote it. It is the reading
that rule asks for rather than a number anybody may keep.

## Evidence

- The actions, read on 2026-08-29. `actions/checkout` stood at v4 against a
  current v7.0.1, `actions/setup-node` at v4 against v7.0.0,
  `actions/upload-pages-artifact` at v3 against v5.0.0 and
  `actions/deploy-pages` at v4 against v5.0.0.
- Two pins were already current and stay. `shivammathur/setup-php` is on v2, and
  2.37.2 is the newest release of it. node is on 24, which is the active LTS
  under the name Krypton, while 26 is the current line and is not an LTS yet.
- `composer outdated --direct` offered one major and three patch levels.
  `phpunit/phpunit` 11.5.56 against 12.5.34, and `friendsofphp/php-cs-fixer`,
  `phpstan/phpstan` and the three Symfony components inside their declared
  constraints.
- What each PHPUnit line requires: 11.5.56 takes `php >=8.2`, 12.5.34 takes
  `php >=8.3`, 13.3.2 takes `php >=8.4.1`. `composer.json` declares `php >=8.2`
  and `ci.yml` runs the suite on 8.2, 8.3 and 8.4.
- The session read the release notes of the raised majors against what these two
  workflows do. `checkout` 7.0.0 refuses a fork's head on `pull_request_target`
  and `workflow_run`, and neither event appears here. `setup-node` 5.0.0 caches
  by itself where `package.json` carries a `packageManager` field, and this
  repository has no `package.json` at all. `upload-pages-artifact` 4.0.0 stopped
  to put dotfiles in the artifact.

## Decided

- The four action pins go to the current major, in `ci.yml` and
  `documentation.yml`.
- Where the floor rules the current major out, the pin takes the newest version
  that floor does allow rather than stays where it was. The floor is what
  `composer.json` declares, not what the machine happens to run.
- `phpunit/phpunit` is that case today and stays at `^11.5`. 12 takes
  `php >=8.3` and 13 takes `>=8.4.1`, against a declared `php >=8.2`. A raise
  drops 8.2, which is a decision about who can install this server rather than
  one about the test runner. Nothing here has asked for it. The 8.2 is today's
  floor and not the rule. When the floor moves, the pin moves with it to
  whatever is newest under the new one.
- `composer update` against the constraints as they stand takes the patch
  levels, so the lock file carries them and no constraint moves.
- `coveredBy: []`. What holds the raise is the two workflows themselves on the
  next push. What holds the PHPUnit line is the 8.2 leg of the matrix.

## Assumed

- That the rendered site carries no dotfile the deployment needs. Nobody read
  it. The render needs the theme from the network, and the artifact the next
  deployment uploads is where the answer is cheapest.
- That a green run on `ubuntu-latest` says nothing about a self-hosted runner,
  which `checkout` 5.0.0 requires at 2.327.1 or newer. This repository has none.

## Wrong if

- The site loses a file after this. That is the dotfile assumption above, and
  the deployment is where it shows.
- `deploy-pages` fails for a permission the workflow's `permissions:` block does
  not grant. Its 4.0.0 asked for `actions: read`, and the block names
  `contents`, `pages` and `id-token` alone. The runs on v4 were green, which is
  what makes this a question about v5 rather than about the block.
- Somebody raises PHPUnit anyway and the 8.2 leg of the matrix goes with it,
  which narrows who can install this package in silence.
- A pin stays where it was after the floor moved. The exception is about what
  the floor allows. So a raised floor makes the old pin wrong on the day of the
  raise rather than at the next read.

## Since then

- Read on 2026-08-29, the day of its write. The floor was the right measure and
  the pin was not. A constraint carries more than one major. So the newest the
  floor allows is a resolution per PHP version rather than one number for all of
  them. `phpunit/phpunit` is `^11.5 || ^12.5 || ^13.0` since, and `D-COD-008`
  carries what the session measured and what the matrix does with it.
