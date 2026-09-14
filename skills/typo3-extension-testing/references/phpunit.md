# PHPUnit unit and functional guidance

Read this after you choose a PHPUnit layer. Let the checkout, the
`project-extension-tests` hint, and versioned documentation decide concrete
package versions, bootstrap APIs, configuration contents, and commands.

## Verify the harness

1. Inspect the harness before you change it. That is `composer.json`, the lock
   file, the installed PHPUnit and `typo3/testing-framework`, and the PHPUnit
   configuration. It is the bootstrap files, the Composer scripts, and CI.
2. Run the narrowest declared PHPUnit discovery or suite command. Tell a missing
   executable, an invalid configuration, a bootstrap failure, and an unavailable
   functional database apart from a failed assertion.
3. If configuration is absent or stale, take its current shape from the
   installed testing framework or the versioned official documentation. Do not
   copy another extension's dependency constraint. Do not point at the core mono
   repository.
4. Preserve one command per useful layer. Give CI the same entrypoint that
   passes locally rather than a second invocation you assemble apart.
5. When `typo3/testing-framework` is missing, use its official compatibility
   information only to form candidates. Let Composer resolve the newest
   candidate that intersects the package's TYPO3, PHP, PHPUnit,
   minimum-stability, and lock constraints. Do not name or write a concrete
   constraint until the solver has accepted it.
6. Select the functional database from existing project or CI infrastructure and
   the behavior under test. SQLite is useful only when the production schema and
   queries support it. It is not the default merely because it needs no service
   container.

## Choose the folders

Keep an established repository layout. Where none exists, `typo3_rule_lookup`
with `documentId="extension/testing/phpunit"` holds the files, whole. Those are
`Build/UnitTests.xml`, `Build/FunctionalTests.xml` and the bootstrap beside
each. It says where they sit in a project, and what each needs after you write
it out.

- Keep runner configuration and bootstraps out of `Tests/`. They describe how
  the suite runs rather than one tested behavior.
- Keep unit and functional trees separate, so each configuration discovers only
  its own layer.
- Mirror the production subject below `Tests/Unit/` when that makes a test easy
  to find. Organize functional tests by behavior or subsystem when several
  production classes participate.
- Put fixtures beside the functional test or its small subsystem. Do not put
  them in one extension-wide bucket whose ownership is unclear.
- If a project already uses another shape that works, adapt it. Do not move
  files for cosmetic consistency. Calculate testsuite paths from the final
  configuration location.

## Write the test

- Unit-test isolated behavior without a TYPO3 boot. Prefer real value objects
  and collaborators. A test whose subject is only mocks is evidence about the
  mock arrangement.
- Use a functional test for dependency injection, configuration, persistence,
  repositories, TCA, DataHandler, routing, database schema, and server-side
  frontend rendering.
- Load only the extensions the behavior needs, derived from the package
  requirements and the subject under test.
- Keep fixtures beside the test, minimal, deterministic, and explicit about
  their expected result. Avoid records from the developer's installation.
- Resolve services from the functional test container when the wiring is part of
  the contract.
- Reset or isolate state that survives between tests. A test that passes alone
  but fails in its containing suite is not finished.
- Add the smallest real assertion that proves the behavior. Never use a vacuous
  assertion to certify the harness.

## Prove it

1. Run the new test or method alone.
2. Run its complete unit or functional suite.
3. Run both suites when shared bootstrap, dependencies, scripts, or CI changed.
4. Run the CI-equivalent command after local execution passes.
5. Report database or environment prerequisites apart from test results.

For a server-rendered frontend response, PHPUnit proves routing, configuration,
markup, and response behavior. It does not execute JavaScript, apply CSS, or
exercise a browser accessibility tree. Use the Playwright guide for those
claims.
