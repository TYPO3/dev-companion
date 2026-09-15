# SITE-03 — Something behaves differently than documented

**Environment:** `E-SITE` · **Contract:** `held`
**Held by:** `ScopeTest::anUnconsultedConfigurationPathIsNotReportedAsAbsent`,
`ProjectTest::whatAnExtensionRegistersIsReadFromItsOwnFiles`,
`Typo3CliTest::aDdevProjectThatIsNotRunningIsReported`,
`ConfigurationLookupTest` in full,
`Typo3RuntimeTest::aTopicWithAnArgumentIsReadOnlyWhereACallerAskedForIt`

> Editors report that a field in one of our record forms is suddenly read-only,
> and I cannot find where that comes from. Nothing in our own configuration does
> it. Find out what is actually configured in this installation.

**What the agent needs from this server**

- The effective value of the configuration involved, after every extension has
  had its say — not the shipped default.
- Which extensions are active in this installation at all.
- The conventions of the subsystem doing it, so the agent knows where such an
  override can come from: form data providers, TCA overrides, event listeners.
- Which backend module the editors are actually in, and what registers it.

**What has to come out of it**

- The configuration value comes from this installation's runtime, and the answer
  says it did — on every covered line. This case runs on `E-SITE`, which unnamed
  is the covered stable one. So the environment alone would never have shown
  that the console command behind this answer exists on two of the four
  (`D-ANS-052`).
- The subsystem conventions explain the mechanism, and the agent then finds the
  concrete override in the checkout rather than being told which one it is.
- Where the server could not ask the installation, the answer says so, and a
  reader can tell that from a value that is empty.

**How it fails**

- The core default quoted as the effective value.
- An unreachable console that produces an answer in the shape of "nothing is
  configured" (`R-ANS-001`, `R-ANS-002`).
