---
description: >-
  Which file system publisher a functional test instance runs, why publishing does nothing for a package inside the public path, and what has to stand at the target for a real failure.
whenToUse: >-
  When a functional test has to drive the system resource publisher rather than stub it — to prove that a publishing failure reaches a caller, or that it does not. It holds from TYPO3 14, where the SystemResource namespace arrived. How a package registers its public resources is a hint instead.
hints:
  - core-tests
---

# Exercising Asset Publishing in a Functional Test

Three facts decide whether such a test exercises anything, and none of them
stands in the code the test calls. A session that reasoned from the class names
alone wrote two tests it discarded and one wrong hypothesis. That is how this
page came to be.

Everything here holds from TYPO3 14, where
`typo3/sysext/core/Classes/SystemResource/` arrived. The older lines have no
publisher to exercise.

## The Symlink Publisher Is Active, and Not Because of the Context

`SYS/SystemResources/filesystemPublishingType` ships as `link`, in the core's
own `Configuration/DefaultConfiguration.php`. A test instance inherits it like
any other installation. So nothing pins it and nothing needs to.

`PublishingConfiguration` falls back to `auto` only where neither an argument
nor that setting supplies a value. It resolves `auto` to `link` under a
development context and to `mirror` otherwise. That branch does not run here. If
you reason from it, you conclude that `Testing` is not `Development` and so the
mirror publisher runs. That is the wrong publisher. Then you provoke a failure
path nothing can enter.

The container holds three publishers. `SymlinkPublisher::canPublish()` answers
true wherever link publishing is on and the host is not Windows.
`MirrorPublisher` and `JunctionPublisher` decline. So nothing reaches their
failure paths while the setting says `link`.

## Publishing Does Nothing for a Package Inside the Public Path

`ResourcePublishingContext` sets `isSourcePublic` when the package path plus the
resource's relative path starts with `Environment::getPublicPath()`.
`DefaultSystemResourcePublisher` skips every resource where that is true.

In a functional test instance the project path is the public path. So the
publisher skips every resource of a package below it and does nothing at all. A
test against such a package concludes that the code under test does nothing.
Point the package path outside the public path to reach the publisher.

## A Regular File at the Target Is What Fails

`SymlinkPublisher` asks `isSymlinkedFile()` or `isSymlinkedDirectory()` about
the target first. It creates the link only where the answer is no. Then it
compares `realpath(source)` with `realpath(target)`, whichever way it got there.

That gives three outcomes, and only one of them is a failure:

- **A regular file at the target.** It is not a symlink, so the publisher
  attempts the link. `symlink()` refuses a path that exists and returns false.
  The publisher raises `PackageAssetsPublishingFailedException` for the
  `symlink` strategy. Set this one up.
- **A symlink that already points at the source.** The publisher reads it as
  published. It creates no link, the two real paths agree, and the call succeeds
  without a word. A link you create in advance proves nothing.
- **A symlink that points anywhere else.** The publisher creates no link either,
  and the real paths disagree. So this fails too, on the comparison rather than
  on the `symlink()` call.

The message a failure carries names the extension and the strategy. So a test
that asserts on it asserts on the word `symlink` rather than on a file name.

## What the Test Instance Pins

No file in the core writes down which `TYPO3_CONF_VARS` a functional instance
sets. Dump `$GLOBALS['TYPO3_CONF_VARS']` from inside a test you then delete;
that is how you read it. Do that before you reason from
`DefaultConfiguration.php` about any other setting. This page settles one of
them, and the same trap lies under the rest.
