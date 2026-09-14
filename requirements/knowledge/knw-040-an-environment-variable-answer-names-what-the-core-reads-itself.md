---
id: R-KNW-040
title: 'An environment variable answer names what the core reads itself'
status: held
heldBy:
  - HintsTest::settingTheEncryptionKeyFromAnExtensionIsBoundToWhereItBreaks
  - HintsTest::whichEnvironmentVariablesTheCoreReadsItselfIsAnswered
---

# R-KNW-040 — An environment variable answer names what the core reads itself

**A question about configuration from the environment gets two answers. One is
the variables TYPO3 reads on its own, the other what a project wires up
itself.**

The answer names the three the bootstrap reads, and the prefixed forms a web
server forwards. It answers `%env()%` as a placeholder of the YAML loader, with
what it therefore reaches and what it does not. It answers everything else, the
encryption key and the database credentials included, as the project's own
`getenv()`. It separates the install-time commands that do accept a fixed set of
variables from what a live installation reads. That is where the belief that the
core reads them comes from.

Where the version boundary of an assignment moves, the answer carries it. An
encryption key set from an extension stands against the majors it still boots
on.

## From

A session that could not verify whether the core reads `TYPO3_ENCRYPTION_KEY` or
`TYPO3_DB_HOST` and answered from its own knowledge instead. It was correct, and
nobody could verify it, because the corpus stated the project half of the
boundary without the core half (2026-07-31).
