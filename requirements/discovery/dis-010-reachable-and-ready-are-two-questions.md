---
id: R-DIS-010
title: 'Reachable and ready are two questions'
status: held
heldBy:
  - Typo3CliTest::aConsoleAlreadyInsideDdevIsReadyThroughItsDirectPhp
  - Typo3CliTest::aStoppedProjectOnHostPhpIsReportedAsAHalfAnswer
  - Typo3CliTest::anInstallWithNoPlatformCheckBoundsNothing
  - Typo3CliTest::thePhpBoundComesFromTheInstallWhereTheManifestStatesNone
---

# R-DIS-010 — Reachable and ready are two questions

**Reachable and ready are two questions.**

Whether a console can run at all depends on what the installation requires
rather than on what its manifest happens to state. The bound Composer wrote into
`vendor/composer/platform_check.php` is the one that stops a boot, and a
distribution that states none in `composer.json` still has one.

A console found on this machine while its project runs elsewhere gets a report
with what that costs, in the text and in the data. A lookup that fails repeats
it where the error alone does not say it. The report states the cost as what the
boot cannot reach, the runtime the project declares and the services it brings.
It never states it as a list of tools. Which answer meets that limit is a
property of the installation rather than of the tool asked. The report carries
**both** steps that end the state, the start and the call after it. An answer
from the weaker source does not change where it stands. So a caller told only to
start the project acts on that and works on from what it just had to stop to
trust. A server that already runs inside that project's DDEV web container is
ready through its direct PHP. The report must not diagnose it as a host with an
unreachable DDEV project merely because the container has no nested `ddev`
binary.

## From

`typo3_server_scope` reported the console as reachable via host PHP 8.3 with the
DDEV project stopped. So five installation-backed tools appeared usable and
nothing said where their answers would come from (2026-07-29).

The caveat written for that named the tools it believed were lost, and the
measurement showed the names wrong. Driven against `.environments/e-site-14.3`
with its DDEV project stopped on 2026-08-04, all seven installation-backed tools
answered. They answered byte for byte what the same calls answered through
`ddev exec` with the project up. The host interpreter had no database driver
compiled in at all; a query from it comes back "could not find driver". So not
one of them had put a query anywhere, and a boot of TYPO3 is not what a stopped
project takes away. Only `typo3_schema_lookup` asks the connection for anything,
and only for the platform. `pdo_sqlite` supplies that with no server, and
`pdo_mysql` fetches it with a connection unless the configuration states
`serverVersion` (`D-DIS-012`).

The same report one layer down, against `.environments/e-site-main` on
2026-08-04: that console was not reachable at all. The base distribution states
neither `config.platform.php` nor `require.php`, so nothing bounded the
interpreter. Host PHP 8.3 passed, and every boot through it died in the platform
check Composer had already written for `>= 8.5.0`.
