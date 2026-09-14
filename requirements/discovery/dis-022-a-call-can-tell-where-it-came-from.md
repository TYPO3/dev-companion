---
id: R-DIS-022
title: 'A call can tell where it came from'
status: held
restsOn: [D-DIS-006]
heldBy:
  - StdioServerTest::itWalksUpToTheInstallationFromInsideIt
  - StdioServerTest::theServerWorksOutWhichInstallationItWasStartedIn
---

# R-DIS-022 — A call can tell where it came from

**A server started inside an installation answers about that installation, from
wherever inside it the session happens to be.**

This is the feature the rest of them depend on. Thirteen of the twenty tools
answer differently once the search finds an installation, and three are not on
offer at all. So a server that works out nothing answers about TYPO3 in general
where the caller asked about a checkout. Nothing in the answer says which of the
two the caller holds.

How the server works it out is not fixed here. Today the entrypoint hands in the
working directory and `Instance` walks up from it, which
[`R-DIS-001`](dis-001-discovery-belongs-to-the-stdio-entrypoint-alone.md)
restricts to that one caller. `TYPO3_DEV_COMPANION_ROOT` states it outright
instead. Nothing in the protocol replaces either. `roots` was the candidate and
now carries a deprecation, which
[`D-DIS-006`](../../decisions/discovery/dis-006-the-installation-stays-worked-out-from-the-directory-the-server-was-started-in.md)
records. Should one arrive, the mechanism may change; this may not.

## From

A deletion of the line that finds it from the entrypoint left all 495 tests
green. Every test that covers discovery hands `Instance` a directory itself and
so covers what happens after somebody does. Nothing covered that somebody does
(2026-08-01).
