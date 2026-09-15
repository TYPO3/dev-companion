# META-02 — Nothing to ask

**Environment:** `E-NONE`, then `E-STOPPED` · **Contract:** `held`
— `R-DIS-006` … `R-DIS-009`, `R-ANS-001`, `R-ANS-005`
**Held by:**
`Typo3CliTest::aDdevProjectThatIsNotRunningIsReported`,
`Typo3CliTest::aMissingConsoleNamesEveryPathThatWasProbed`,
`InstanceTest::anInstallationThatAppearsDuringTheSessionIsFound`,
`Typo3CliTest::aStoppedProjectThisMachineCanRunIsAskedAgainAfterItStarts`,
`Typo3CliTest::aStoppedProjectIsAskedAgainAfterItStarts`,
`LabelSearchTest::aConsoleThatCannotRunIsStillUnanswered`. Not guarded: that a
stopped project returns the icons and labels it returns when it runs is a
property of an environment. No test has one. A measure stands in, in
`.environments/e-site-14.3` on 2026-08-04, and the read is on `D-ANS-005`.

> Which icons can I use for a "publish" action, and is there already a label for
> "Publish page"?

Run it twice: once in a directory with no TYPO3 anywhere above it, and once in a
site project whose DDEV stands stopped. The prompt stays the same; only the
environment changes.

The two halves are not two grades of the same failure, and the criteria below
say which is which. With nothing above the directory there is no installation to
ask, and the answer has to say so. With the project stopped there is one, and on
every covered line this machine can reach its console. So the prompt gets its
answer, and the state lives in `typo3_server_scope` rather than in the answer.

**What has to come out of it**

- With nothing above the directory, neither lookup answers "there are no such
  icons" or "there is no such label". Both say the server could not ask the
  installation, and why.
- With the project stopped, the prompt gets an answer rather than a refusal. The
  console reached through an interpreter of this machine returns the icons and
  labels the project returns when it runs. It says it read them from the
  installation. A lookup may not report two states it cannot tell apart as
  though it could.
- Where discovery failed, the answer names where it looked.
- Nothing starts as a side effect, in either environment. `typo3_server_scope`
  reports the stopped project with the command that would start it. That is
  where that state lives, and the only answer that carries it.
- The answer names the way out where there is nothing to ask: the two
  environment variables that end the guesswork.
- After the user starts DDEV and asks `typo3_server_scope` again in the same
  session, it answers differently. The server reaches the console through DDEV
  on the runtime the project declares, and the caveat is gone. A negative is not
  remembered (`R-DIS-009`).

**How it fails**

- An empty result shaped like a complete one, and the agent concluding the
  extension registers nothing.
- DDEV started by the agent because a lookup wanted an answer.
- A session that needs a restart after the installation became reachable.
