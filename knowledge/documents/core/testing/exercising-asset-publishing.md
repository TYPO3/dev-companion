---
description: >-
  Which file system publisher a functional test instance actually runs, why publishing is a no-op for a package inside the public path, and what has to be at the publishing target for a real failure to be raised.
whenToUse: >-
  When a functional test has to drive the system resource publisher rather than stub it — proving that a publishing failure reaches a caller, or that it does not. It holds from TYPO3 14, where the SystemResource namespace arrived. Registering a package's public resources is a hint instead.
hints:
  - core-tests
---

# Exercising Asset Publishing in a Functional Test

Three facts decide whether such a test exercises anything, and none of them is
in the code the test calls. Reasoning from the class names alone costs two
discarded tests and a wrong hypothesis, which is how this page was written.

Everything here holds from TYPO3 14, where
`typo3/sysext/core/Classes/SystemResource/` arrived. On the older lines there is
no publisher to exercise.

## The Symlink Publisher Is Active, and Not Because of the Context

`SYS/SystemResources/filesystemPublishingType` ships as `link`, in the core's
own `Configuration/DefaultConfiguration.php`. A test instance inherits it like
any other installation, so nothing pins it and nothing needs to.

`PublishingConfiguration` falls back to `auto` only where neither an argument
nor that setting supplies a value, and resolves `auto` to `link` under a
development context and to `mirror` otherwise. That branch does not run here.
Reasoning from it — the `Testing` context is not `Development`, therefore the
mirror publisher — reaches the wrong publisher and then provokes a failure path
nothing can enter.

Of the three publishers the container holds, `SymlinkPublisher::canPublish()`
answers true wherever link publishing is on and the host is not Windows.
`MirrorPublisher` and `JunctionPublisher` decline, so their failure paths are
unreachable while the setting says `link`.

## Publishing Is a No-Op for a Package Inside the Public Path

`ResourcePublishingContext` sets `isSourcePublic` from whether the package path
plus the resource's relative path starts with `Environment::getPublicPath()`,
and `DefaultSystemResourcePublisher` skips every resource where it is true.

In a functional test instance the project path is the public path. So every
resource of a package below it is skipped, publishing does nothing at all, and a
test written against such a package concludes that the code under test does
nothing. Point the package path outside the public path to reach the publisher.

## A Regular File at the Target Is What Fails

`SymlinkPublisher` asks `isSymlinkedFile()` or `isSymlinkedDirectory()` about
the target first, and only creates the link where the answer is no. Then it
holds `realpath(source)` against `realpath(target)` whichever way it got there.

That gives three outcomes, and only one of them is a failure:

- **A regular file at the target.** Not a symlink, so the link is attempted;
  `symlink()` refuses a path that exists and returns false, and
  `PackageAssetsPublishingFailedException` is raised for the `symlink` strategy.
  This is the one to set up.
- **A symlink already pointing at the source.** Read as published: no link is
  created, the two real paths agree, and the call succeeds silently.
  Pre-creating one to provoke a failure proves nothing.
- **A symlink pointing anywhere else.** No link is created either, and the real
  paths disagree, so this fails as well — on the comparison rather than on the
  `symlink()` call.

The message a failure carries names the extension and the strategy, so a test
asserting on it asserts on the word `symlink` rather than on a file name.

## What the Test Instance Pins

Which `TYPO3_CONF_VARS` a functional instance sets is not written down anywhere
in the core, and dumping `$GLOBALS['TYPO3_CONF_VARS']` from inside a test that
is then deleted is how it is read. Do that before reasoning from
`DefaultConfiguration.php` about any other setting: this page settles one of
them and the same trap is under the rest.
