# CORE-05 — A test fails and nobody knows on what

**Environment:** `E-CORE` · **Contract:** `held`
**Held by:** `HintsTest::aSuiteIsFoundByItsName`,
`HintsTest::theInvocationNotesApplyToEverySuite`,
`HintsTest::theSuiteListItselfIsFilteredByTheBranchItIsAskedFor`

> A functional test fails for me locally but passes in CI. It is in
> `typo3/sysext/core/Tests/Functional/DataHandling/`. Get it running the way CI
> runs it and find out what is different.

**What the agent needs from this server**

- The suite behind that directory and the targeted invocation.
- The options that make a local run match CI: the PHP version, the database
  vendor and version, the flags that select them.
- The other checks that exist next to the tests, so "runs green" means what CI
  means by it.

**What has to come out of it**

- Runnable commands, targeted at the failing test, with the option that pins the
  database vendor and the one that pins the PHP version.
- The answer states the difference between the unit, functional and acceptance
  suites where it matters for the diagnosis.

**How it fails**

- A `vendor/bin/phpunit` invocation instead of the core's own script.
- Suites recommended by name with no command, or commands with invented flags.
