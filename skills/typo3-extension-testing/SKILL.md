---
name: typo3-extension-testing
description: 'Set up, extend, repair or run tests and static quality checks for a TYPO3 project or extension: missing test infrastructure, PHPUnit unit and functional tests, fixtures, Playwright browser and accessibility tests, PHPStan, php-cs-fixer, baselines and a failing check.'
compatibility: Needs the typo3-dev-companion MCP server, which owns every lookup this workflow routes to and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Testing

Establish or grow the smallest useful test and static-quality surface at the
correct layer. Make the first green run part of setup, and run only commands
supported by the checkout. Keep this skill as routing and workflow; never retain
version-specific APIs, paths, dependency constraints, or commands that the
installation or checkout owns.

## Establish the test surface

Work through [references/base.md](references/base.md) first — it fixes the order
every task here starts in and why that order is not interchangeable. Two of its
answers decide this workflow before any test is written: the commands
`typo3_project_describe` reports are the only ones that exist in this
repository, and the layers `typo3_extension_describe` reports below `Tests/` are
what the extension has today — an empty list is the answer that there is no
harness yet.

Then, for this workflow:

- `typo3_documentation_lookup` with several short English queries and the target
  TYPO3 version when dependency setup, bootstrapping, fixtures, browser
  configuration, or an API needs confirmation.
- Verify that the harness for every relevant layer can discover and run its
  tests. Treat missing or broken infrastructure as a prerequisite of the
  requested work, not as a separate kind of task and not as a reason to force
  the behavior into another layer.
- Read the target code, existing tests and fixtures, test configuration,
  dependency manifests, CI, and the development environment.

If a lookup is unavailable, state the gap separately from a lookup that found no
match. Do not replace current project evidence with remembered TYPO3 setup.

## Choose the layer and its owner

Read [references/checklist.md](references/checklist.md) when selecting layers,
establishing missing infrastructure, choosing commands, or auditing coverage.
After selecting a layer, read only its implementation guide:

- [references/phpunit.md](references/phpunit.md) for unit or functional tests.
- [references/playwright.md](references/playwright.md) for browser,
  accessibility, or visual tests.
- [references/static-quality.md](references/static-quality.md) for static
  analysis, coding standards, and the commands that run them.

- Prefer a unit test for isolated logic without TYPO3 state or persistence.
- Use a functional test when TYPO3 bootstrapping, configuration, database
  schema, DataHandler, repositories, services, or integration between framework
  components is part of the behavior.
- Use a browser test for rendered user journeys, backend interaction,
  JavaScript, or accessibility behavior that cannot be established below the UI.
- Use static analysis and coding standards for the defects and the style no test
  observes. A task that asks for them establishes them whether or not the
  project already runs them; a task that does not ask extends what is there and
  reports what is missing rather than introducing a check nobody requested.
- Keep unit and functional infrastructure with the extension whose PHP it
  exercises. Keep browser infrastructure with the runnable project, because it
  needs a served site rather than an extension package alone.
- Establish only the layers the task can justify. A setup request does not
  require every possible test runner.

## Establish or repair the required harness

Before adding or extending coverage, fix any missing or broken prerequisite for
the selected layer. For an explicit setup request, this is the requested work;
for a review-only request, report the defect without changing it.

1. Determine compatible development dependencies from the project's constraints,
   installed packages, Composer resolution, and versioned documentation. Add a
   dependency only when changes are in scope and the selected layer requires it;
   never guess its version.
2. Take configuration and bootstrap templates from the installed dependency or
   the source named by `typo3_hint_lookup`. Copy and adapt templates that say
   they are examples; do not point extension suites into a core checkout.
   `typo3_reference_list` says which extensions the core ships as worked
   examples of its own conventions and what each one is a reference for — the
   browser suite and the static analysis setup among them. One of those is the
   currently-passing form of the harness being established here, which is what a
   template copied out of a manual is not.
3. Preserve working configuration, scripts, and CI. Extend them instead of
   creating a parallel harness.
4. Give each selected layer one stable local command before adding CI. Derive
   functional database settings and browser URLs from the project's environment;
   do not commit credentials or machine-specific hosts.
5. For unit or functional tests, establish the suite configuration, bootstrap,
   test directories, extension loading, and environment the returned guidance
   requires. Never translate a core-only `runTests.sh` command into an extension
   command.
6. For browser tests, require a runnable site and establish project-owned runner
   configuration, scripts, artifacts, and one real target. Choose host,
   container, or dedicated browser image from the project rather than imposing
   one topology.
7. For static analysis and coding standards, establish one project-owned command
   per check and keep the command that reports apart from the one that writes.
   Fix a new finding rather than recording it in a baseline, and keep automatic
   formatting inside the first-party paths the project intends it to touch.
8. Make CI call the same commands that passed locally. Add a version matrix only
   for combinations the package declares and the dependency solver accepts.

## Add or extend tests

- Follow nearby passing tests and the established harness. If the required layer
  is missing, establish it first instead of forcing the behavior into a cheaper
  layer.
- Preserve a regression test that fails for the observed defect before applying
  its fix when practical.
- Keep fixtures minimal and deterministic. Avoid unrelated site data, execution
  order, wall-clock timing, and external services.
- Put reusable setup at the narrowest scope that removes meaningful duplication.
- Test observable behavior and public contracts; avoid assertions tied only to
  implementation details.
- Distinguish a broken runner, a missing environment prerequisite, and a failing
  assertion before changing production code.

**Where the failing assertion is a defect in code another workflow owns, invoke
the skill that owns it before the code is changed.** That is a step, not a note
about ownership: load the skill by name and work from it. What crosses over is
the failing test, what it establishes and the paths it runs over; the test
itself stays here and runs again on what comes back.

## Prove the result

1. Prove setup with a meaningful test at every layer established by the task. Do
   not add `assertTrue(true)` or production code whose only purpose is to give
   the harness something to test. If no unit-testable behavior exists, prove
   discovery and report the unit suite as empty.
2. Run the narrowest relevant test first, then its containing local suite.
3. Run the declared CI-equivalent commands after the local commands pass.
4. For browser work, execute at least one real spec and confirm its expected
   artifact or report is produced.
5. For a static check, run it again after its fix command and inspect the
   working tree for files the fixer touched outside the intended scope.
6. Report the exact commands run, results, files added or changed, and checks
   not run with the reason.
7. Draft the message for each commit with `typo3_commit_message_guide` and
   `workflow="project"`.
   [references/static-quality.md](references/static-quality.md) says where a
   formatting pass is split off and in which order the commits go; what each of
   them says is this tool's answer.

This skill owns testing and static-quality infrastructure, the changes they
require, and the execution of both. A broad conformance audit is
`typo3-extension-health`, a documentation rewrite is
`typo3-extension-documentation`, a backend module is
`typo3-backend-module-development` and a content element is
`typo3-content-element-development`. Hand that work to its owner at the verified
point, stop before editing its files, and retain only the testing part.
