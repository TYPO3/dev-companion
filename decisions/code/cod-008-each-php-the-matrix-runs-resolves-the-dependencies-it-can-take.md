---
id: D-COD-008
title: 'Each PHP the matrix runs resolves the dependencies it can take'
date: 2026-08-29
status: open
readings:
  - 2026-09-01
coveredBy: []
---

# D-COD-008 — Each PHP the matrix runs resolves the dependencies it can take

**`ci.yml` resolves per leg instead of installing the lock, so each supported
PHP runs the newest dependency it can take — PHPUnit 11, 12 or 13.**

`D-COD-007` had PHPUnit stay at `^11.5` because 12 needs PHP 8.3 and this
package declares 8.2. That reading held the floor and gave up the ceiling, and
the ceiling was available: a constraint carries every major, and the resolver
picks per PHP version.

## Evidence

- Measured on 2026-08-29 with `composer config platform.php` standing in for
  each version. PHP 8.2 resolves PHPUnit 11.5.56, 8.3 resolves 12.5.34, and
  13.3.2 arrives at 8.4.1 rather than at 8.4, because that is what it requires.
- The suite is green on each. 11 and 12 were run on the host, 12 whole with
  `OK (2248 tests, 70910 assertions)`; 13 was run in a container on PHP 8.5,
  where one smoke test fails for the container rather than the version —
  `StdioServerTest::aProjectWhoseSkillsNobodyHasUpdatedHasThemPutBack` fails the
  same way there under PHPUnit 11 on PHP 8.3, which is the combination the host
  passes.
- `composer run-script ci` was run whole on PHP 8.5: the lint, the coding
  guidelines and the static analysis pass there, so a leg for it is green
  wherever `git` is on the machine.
- PHP 8.5 deprecated `SplObjectStorage::contains()` and `::attach()`, both of
  them in `Manual\Documentation`. Nothing on 8.2 to 8.4 says so, and the run
  that found them is the argument for the leg: `composer.json` declares
  `php >=8.2` with no upper end, so 8.5 was already claimed.

## Decided

- `phpunit/phpunit` is `^11.5 || ^12.5 || ^13.0`, and the resolver decides which
  one a PHP version gets.
- `ci.yml` runs `composer update` rather than `composer install`, because the
  lock holds one resolution and installing it would have run one PHPUnit on
  every leg. What the matrix is for is what each version can take.
- The matrix gains 8.5, which is what `php >=8.2` already promises.
- The lock stays committed and `documentation.yml` still installs it, so one
  workflow reads the resolution this checkout wrote and the other reads what
  each version resolves today.
- No `config.platform.php`. It would pin the resolution for everyone, CI
  included, which is the opposite of what this decides.

## Assumed

- That resolving per leg is wanted for every dependency and not only for
  PHPUnit. A patch release that breaks a leg is then CI's news rather than the
  next `composer update`'s, which is the trade taken here.
- That a green leg resolved this way is what this package means by supporting a
  PHP version.

## Wrong if

- A leg goes red for somebody else's release often enough that a red CI stops
  being read. Then the lock belongs back in the legs and the ceiling needs
  another way to be tested.
- Somebody on PHP 8.2 cannot `composer install` this checkout, because the lock
  carries what the maintainer's PHP resolved — 12.5.34 today. The cheap repair
  is `composer update` on their machine; the expensive one is a floor-resolved
  lock, which is what `config.platform.php` above would buy.
