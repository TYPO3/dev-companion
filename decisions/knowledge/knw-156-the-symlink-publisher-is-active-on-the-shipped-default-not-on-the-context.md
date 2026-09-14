---
id: D-KNW-156
title: The symlink publisher is active on the shipped default, not on the context
date: 2026-09-09
status: open
coveredBy: []
---

# D-KNW-156 — The symlink publisher is active on the shipped default, not on the context

**`SYS/SystemResources/filesystemPublishingType` ships as `link`, so the `auto`
resolution against the application context runs only where a configuration sets
`auto` back.**

A session reasoned from `auto` to the mirror publisher, provoked that
publisher's failure path, and got no message at all. Its conclusion was right
and its mechanism was not.

## Evidence

- **The report.**
  [`feedback/2026-09-09-180725`](../../feedback/archive/2026-09-09-180725-functional-test-instances-publish-assets-by.md),
  `/home/benji/projects/typo3-cms`, `claude-opus-5[1m]`. Three rounds and two
  throwaway functional tests to learn one configuration value. The task had to
  raise a real publisher failure rather than stub one.
- **Read in `.checkouts/` on 2026-09-09.**
  `typo3/sysext/core/Configuration/DefaultConfiguration.php` carries
  `'filesystemPublishingType' => 'link'` on `main` and on `14.3` alike.
  `PublishingConfiguration::__construct()` falls back to `'auto'` only where
  neither an argument nor that setting supplies a value. It resolves `'auto'` to
  `link` under a development context and to `mirror` otherwise.
- **So the report's mechanism is wrong and its conclusion holds.** It names the
  functional test instance as what pins the value. What pins it is the core's
  own shipped default, which a test instance inherits like any other
  installation. The symlink publisher is active either way.
- **The second fact it names is there.** `ResourcePublishingContext` sets
  `isSourcePublic` from
  `str_starts_with($packagePath . $relativePath, Environment::getPublicPath())`
  and `DefaultSystemResourcePublisher` skips a resource where it is true. In an
  instance whose project path is its public path, that is every resource under
  it.
- **The namespace is new.** `typo3/sysext/core/Classes/SystemResource/` exists
  on `14.3` and `main` and on neither `13.4` nor `12.4`, so anything about it
  binds from 14.

## Decided

- **Taken on as step 1a.** Nothing below `knowledge/` says which publisher a
  functional test runs under or why the publisher does nothing for a package
  inside the public path. Both are what the session spent its rounds on.
- **The page states the mechanism as the shipped default rather than as a
  test-instance pin.** A page that says the test instance sets it sends the next
  reader after a line that is not there. It leaves them in the belief that the
  value is different outside a test.
- **Against writing the report's own account in.** Its first two rounds rest on
  an `auto` resolution in play, which the checkout says it is not. The judging
  run that reads a checkout is what keeps that out, the same rule that kept the
  one-paragraph body rule out of `D-KNW-155`.
- The card carries `normal`. One session, and what it cost was two throwaway
  functional tests and a wrong first hypothesis rather than the task.

## Assumed

- That the shipped default has been `link` since the namespace arrived. Read at
  the tip of `14.3` and of `main` and not along either branch's history.

## Wrong if

- A session reports an installation where the publisher type resolves through
  `auto` with nobody who set it. Then the default is not what decides and the
  context is back in the picture.
- The default changes on a branch this server covers. Then the statement needs
  its own `since` inside the 14 boundary rather than rests on the namespace's.

## Since then

Written on 2026-09-09 as `core/testing/exercising-asset-publishing`, and it
moved a ranking on the way in. The word "throwaway" is rare in this corpus and
`core/testing/proving-a-rendering` owns it. So two uses of it here dropped that
page's sections below the floor. A query that had handed the page over whole
handed one section instead. The word came out. `D-ANS-002` names corpus length
as one of the three things the matcher computes a rank from. This is the first
recorded case of a new page that changes another's answer.
