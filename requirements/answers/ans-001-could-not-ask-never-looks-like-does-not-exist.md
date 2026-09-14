---
id: R-ANS-001
title: '"Could not ask" never looks like "does not exist"'
status: held
restsOn: [D-ANS-005]
heldBy:
  - ScopeTest::anUnconsultedConfigurationPathIsNotReportedAsAbsent
  - StdioServerTest::aQuestionThatCannotBeAnsweredHereIsStillAnAnswer
  - ToolContractTest::aQuestionThatCannotBeAnsweredHereSaysOnlyThat
  - ToolContractTest::anInstallationBackedSchemaOffersEitherShape
  - ToolContractTest::onlyOneClassBuildsTheUnsupportedAnswer
---

# R-ANS-001 — "Could not ask" never looks like "does not exist"

**An answer that means "could not ask" never has the shape of one that means
"does not exist".**

A tool that cannot reach the installation answers with `unsupported` and states
nothing else. No count to read as a count, no flag to read as a fact, no empty
list that stands in for a result. Beside it stands only the caller's own
arguments, echoed back. `unsupported` carries a `cause`, the reason, where
discovery looked, and the setting the server could not use. The cause is
`no-installation`, `misconfigured` or `installation-not-answering`.

`Result\Unsupported` is the only place that builds that shape, so no path
reaches it without a reason to hand over. `answeredBy` says which of the two
sources answered and has no case for neither, because that is this key instead.
The output schema declares the result and the unsupported answer as `oneOf`. So
a hit promises every field it always did, and an answer with both is invalid.

## From

Two feedback that asked that the unavailable case stop to look like an empty
one. A client that twice concluded an extension registered no icons and no
labels (2026-07-29). A read of the tools against it on 2026-08-02 found the
shape still in the code. `typo3_icon_lookup` answered a directory with no
installation with `matchCount: 0`, `suggestionCount: 0` and `exactMatch: false`.
That is field for field the miss it emits against a reachable one.
`typo3_extension_describe` reported `answeredBy: "nothing"` for every miss,
against an installation that had just listed 27 packages included.
