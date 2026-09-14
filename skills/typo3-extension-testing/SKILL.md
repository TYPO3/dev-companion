---
name: typo3-extension-testing
description: 'Set up, extend, repair or run tests and static quality checks for a TYPO3 project or extension: missing test infrastructure, PHPUnit unit and functional tests, fixtures, Playwright browser and accessibility tests, PHPStan, php-cs-fixer, baselines and a failing check.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Testing

Establish or grow the smallest useful test and static-quality surface at the
correct layer. Make the first green run part of the setup, and run only commands
the checkout supports. Keep this skill as routing and workflow. Never keep
version-specific APIs, paths, dependency constraints, or commands that the
installation or the checkout owns.

## Establish the test surface

Work through [references/base.md](references/base.md) first. It fixes the order
every task here starts in and says why that order is not interchangeable. Two of
its answers decide this workflow before you write any test. The commands
`typo3_project_describe` reports are the only ones that exist in this
repository.

The layers `typo3_extension_describe` reports below `Tests/` are what the
extension has today.

An empty list is the answer that there is no harness yet.

Then, for this workflow:

- `typo3_documentation_lookup` with several short English queries and the target
  TYPO3 version. Use it when dependency setup, bootstrap, fixtures, browser
  configuration, or an API needs confirmation.
- Verify that the harness for every relevant layer can discover and run its
  tests. Treat missing or broken infrastructure as a prerequisite of the
  requested work. It is not a separate kind of task, and it is no reason to
  force the behavior into another layer.
- Read the target code, existing tests and fixtures, test configuration,
  dependency manifests, CI, and the development environment.

If a lookup is unavailable, state the gap apart from a lookup that found no
match. Do not replace current project evidence with a TYPO3 setup you recall.

## Choose the layer and its owner

Read [references/checklist.md](references/checklist.md) when you select layers,
establish missing infrastructure, choose commands, or audit coverage. After you
select a layer, read only its implementation guide:

- [references/phpunit.md](references/phpunit.md) for unit or functional tests.
- [references/playwright.md](references/playwright.md) for browser,
  accessibility, or visual tests.
- [references/static-quality.md](references/static-quality.md) for static
  analysis, coding standards, and the commands that run them.

- Prefer a unit test for isolated logic without TYPO3 state or persistence.
- Use a functional test when the behavior involves TYPO3 bootstrap,
  configuration, database schema, DataHandler, repositories or services. Use one
  for integration between framework components.
- Use a browser test for rendered user journeys, backend interaction,
  JavaScript, or accessibility behavior nothing below the UI can establish.
- Use static analysis and coding standards for the defects and the style no test
  observes. A task that asks for them establishes them whether or not the
  project already runs them. A task that does not ask extends what is there and
  reports what is missing. It introduces no check nobody requested.
- Keep unit and functional infrastructure with the extension whose PHP it
  exercises. Keep browser infrastructure with the runnable project, because it
  needs a served site rather than an extension package alone.
- Establish only the layers the task can justify. A setup request does not
  require every possible test runner.

## Establish or repair the required harness

Before you add or extend coverage, fix any missing or broken prerequisite for
the selected layer. For an explicit setup request, this is the requested work.
For a review-only request, report the defect without changing it.

1. Determine compatible development dependencies from the project's constraints,
   the installed packages, the Composer resolution, and the versioned
   documentation. Add a dependency only when changes are in scope and the
   selected layer requires it. Never guess its version.
2. Take configuration and bootstrap templates from the installed dependency or
   the source `typo3_hint_lookup` names. Copy and adapt templates that say they
   are examples. Do not point extension suites into a core checkout.

   `typo3_reference_list` says which extensions the core ships as worked
   examples of its own conventions. It says what each one is a reference for.
   The browser suite and the static analysis setup are among them. One of those
   is the form of the harness you establish here that passes today. A template
   copied out of a manual is not.
3. Preserve configuration, scripts, and CI that work. Extend them instead of a
   parallel harness.
4. Give each selected layer one stable local command before you add CI. Derive
   functional database settings and browser URLs from the project's environment.
   Do not commit credentials or machine-specific hosts.
5. For unit or functional tests, establish what the returned guidance requires.
   That is the suite configuration, the bootstrap, the test directories, the
   extension load, and the environment. Never translate a core-only
   `runTests.sh` command into an extension command.
6. For browser tests, require a runnable site. Establish project-owned runner
   configuration, scripts, artifacts, and one real target. Choose host,
   container, or dedicated browser image from the project. Do not impose one
   topology.
7. For static analysis and coding standards, establish one project-owned command
   per check. Keep the command that reports apart from the one that writes. Fix
   a new finding rather than record it in a baseline. Keep automatic formatting
   inside the first-party paths the project intends it to touch.
8. Make CI call the same commands that passed locally. Add a version matrix only
   for combinations the package declares and the dependency solver accepts.

## Add or extend tests

- Follow nearby tests that pass and the established harness. If the required
  layer is missing, establish it first. Do not force the behavior into a cheaper
  layer.
- Keep a regression test that fails for the observed defect before you apply its
  fix, when practical.
- Keep fixtures minimal and deterministic. Avoid unrelated site data, execution
  order, wall-clock time, and external services.
- Put reusable setup at the narrowest scope that removes meaningful duplication.
- Test observable behavior and public contracts. Avoid assertions tied only to
  implementation details.
- Tell a broken runner, a missing environment prerequisite, and a failing
  assertion apart before you change production code.

**Where the failing assertion is a defect in another workflow's code, invoke the
skill that owns it before the fix.** That is a step, not a note about ownership.
Load the skill by name and work from it. What crosses over is the failing test,
what it establishes and the paths it runs over. The test itself stays here and
runs again on what comes back.

## Prove the result

1. Prove the setup with a meaningful test at every layer the task established.
   Do not add `assertTrue(true)` or production code whose only purpose is to
   give the harness something to test. If no unit-testable behavior exists,
   prove discovery and report the unit suite as empty.
2. Run the narrowest relevant test first, then its containing local suite.
3. Run the declared CI-equivalent commands after the local commands pass.
4. For browser work, execute at least one real spec. Confirm that it produces
   its expected artifact or report.
5. For a static check, run it again after its fix command. Inspect the tree for
   files the fixer touched outside the intended scope.
6. Report the exact commands you ran, the results, and the files you added or
   changed. Report the checks you did not run, with the reason.
7. Draft the message for each commit with `typo3_commit_message_guide` and
   `workflow="project"`.
   [references/static-quality.md](references/static-quality.md) says where you
   split a formatting pass off and in which order the commits go. What each of
   them says is this tool's answer.

This skill owns testing and static-quality infrastructure, the changes they
require, and the execution of both. A broad conformance audit is
`typo3-extension-health`. A documentation rewrite is
`typo3-extension-documentation`. A backend module is
`typo3-backend-module-development`, and a content element is
`typo3-content-element-development`. Hand that work to its owner at the verified
point. Stop before you edit its files, and keep only the testing part.
