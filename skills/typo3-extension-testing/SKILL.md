---
name: typo3-extension-testing
description: 'Set up, extend, repair or run tests and static quality checks for a TYPO3 project or extension: missing test infrastructure, PHPUnit unit and functional tests, fixtures, Playwright browser and accessibility tests, PHPStan, php-cs-fixer, baselines and a failing check.'
compatibility: Needs the typo3-dev-companion MCP server, which provides every lookup this workflow calls and publishes this skill together with the references/base.md it opens on. Install it from github.com/TYPO3/dev-companion and run typo3-dev-companion install in the project. A copy taken out of that repository's skills directory alone has neither the tools nor that base file.
---

# TYPO3 Extension Testing

This skill covers the tests and the static quality checks of a TYPO3 project or
extension. Use it to set up or repair a test harness, and to add and run tests.
It stops at the code under test. A defect a test finds in other code goes to the
skill for that code. "Where this skill stops" names them.

**Add the smallest useful set of tests at the correct layer.** The first green
run is part of the setup. Run only commands the checkout supports.

Keep this skill as routing and workflow: it says which tool to ask and in which
order. Versions, APIs, paths, dependency constraints and commands come from the
installation and the checkout. A copy of them here would go out of date in
somebody else's project.

## Steps at a glance

1. Find out which tests and checks the project has today.
2. Choose the layer that can observe the behaviour.
3. Set up or repair the harness for that layer.
4. Add or extend the tests.
5. Prove the result and report it.

## 1. Find out what the project has

1. Work through [references/base.md](references/base.md) first. It fixes the
   order every task starts in. Two of its answers decide this workflow:

   - **The commands.** The commands `typo3_project_describe` reports are the
     only ones this repository has.
   - **The test layers.** `typo3_extension_describe` lists the layers the
     extension has below `Tests/` today. An empty list means that there is no
     harness yet.

2. Verify that the harness for every relevant layer can discover and run its
   tests. A missing or broken harness is part of the requested work. It is not a
   separate task, and it is no reason to move the behaviour into another layer.

3. Read the target code, its tests and fixtures, the test configuration, the
   dependency manifests, CI and the development environment.

4. Call `typo3_documentation_lookup` with several short English queries and the
   target TYPO3 version. Do this when the dependency setup, the bootstrap, the
   fixtures, the browser configuration or an API needs confirmation.

If a lookup is unavailable, report that apart from a lookup that found no match.
Do not replace what the project shows with a TYPO3 setup you remember.

## 2. Choose the layer

Read [references/checklist.md](references/checklist.md) when you choose layers,
set up missing infrastructure, choose commands or audit coverage. After you
choose a layer, read only its implementation guide:

- [references/phpunit.md](references/phpunit.md) for unit or functional tests.
- [references/playwright.md](references/playwright.md) for browser,
  accessibility or visual tests.
- [references/static-quality.md](references/static-quality.md) for static
  analysis, coding standards and the commands that run them.

Choose the lowest layer that can observe the behaviour:

- **Unit test** — isolated logic without TYPO3 state or persistence.

- **Functional test** — behaviour that needs the TYPO3 bootstrap, configuration,
  the database schema, DataHandler, repositories or services. Also the
  integration between framework components.

- **Browser test** — rendered user journeys, backend interaction, JavaScript, or
  accessibility behaviour that no lower layer can show.

- **Static analysis and coding standards** — the defects and the style that no
  test observes. A task that asks for them establishes them whether or not the
  project already runs them. A task that does not ask for them extends what is
  there and reports what is missing. It adds no check that nobody requested.

Set up only the layers the task can justify. A setup request does not need every
possible test runner.

Put each harness where its tests run:

- Keep unit and functional infrastructure with the extension whose PHP the tests
  exercise.
- Keep browser infrastructure with the runnable project. A browser test needs a
  served site, and an extension package alone has none.

## 3. Set up or repair the harness

Fix every missing or broken prerequisite of the chosen layer before you add
tests. For an explicit setup request, this is the requested work. For a
review-only request, report the defect and do not change it.

1. **Dependencies.** Find compatible development dependencies from the project's
   constraints, the installed packages, the Composer resolution and the
   versioned documentation. Add a dependency only when changes are in scope and
   the layer needs it. Never guess its version.

2. **Configuration and bootstrap.** Take the templates from the installed
   dependency or from the source `typo3_hint_lookup` names. Copy and adapt the
   templates that say they are examples. Do not point extension suites into a
   core checkout.

   Then call `typo3_reference_list`. It names the extensions the core ships as
   examples of its conventions. It says what each one is an example of. The
   browser suite and the static analysis setup are among them. Where one of them
   covers your layer, follow its form, because it passes today. A template
   copied out of a manual gives no such proof.

3. **What already works.** Keep configuration, scripts and CI that work. Extend
   them, and do not build a second harness beside them.

4. **One local command per layer.** Give each layer one stable local command
   before you add CI. Take the functional database settings and the browser URLs
   from the project's environment. Do not commit credentials or hosts of one
   machine.

5. **Unit and functional tests.** Set up what the returned guidance requires.
   That is the suite configuration, the bootstrap, the test directories, the
   loaded extensions and the environment. Never translate a core-only
   `runTests.sh` command into an extension command.

6. **Browser tests.** Require a runnable site. Put the runner configuration, the
   scripts, the artifact paths and one real target into the project. Choose
   where the browser runs from what the project uses: the host, a container or a
   dedicated browser image. Do not impose one setup.

7. **Static analysis and coding standards.** Give each check one command in the
   project. Keep the command that reports apart from the command that writes.
   Fix a new finding, and do not add it to a baseline. Keep automatic formatting
   inside the first-party paths the project intends it to change.

8. **CI.** Make CI call the same commands that passed locally. Add a version
   matrix only for combinations the package declares and the dependency solver
   accepts.

## 4. Add or extend the tests

- Follow the nearby tests that pass and the harness from step 3. If the layer
  the behaviour needs is missing, set it up first. Do not move the behaviour
  into a lower layer instead.

- Write a regression test that fails on the observed defect before you apply the
  fix, where that is practical.

- Keep fixtures minimal and deterministic. Avoid unrelated site data, a fixed
  execution order, the wall-clock time and external services.

- Put shared setup at the narrowest scope that removes real duplication.

- Test observable behaviour and public contracts. Avoid assertions that only
  check implementation details.

- Before you change production code, find out what failed: the runner, the
  environment or an assertion.

**When a failed assertion shows a defect in another skill's code, invoke the
skill that covers it before the fix.** Load that skill by name and work from it.
Give it the failing test, what the test shows and the paths it runs over. The
test stays in this skill, and you run it again on the fixed code.

## 5. Prove the result

1. Prove the setup with a meaningful test at every layer you set up. Do not add
   `assertTrue(true)`, or production code whose only purpose is to give the
   harness something to test. If no behaviour can be unit-tested, prove that the
   unit suite is discovered and report it as empty.

2. Run the narrowest relevant test first, then the local suite that contains it.

3. Run the commands that CI runs after the local commands pass.

4. For browser tests, run at least one real spec. Confirm that it writes its
   expected artifact or report.

5. For a static check, run the check again after its fix command. Look for files
   the fixer changed outside the intended paths.

6. Report the exact commands you ran, their results, and the files you added or
   changed. Report every check you did not run, with the reason.

7. Write the message for each commit with `typo3_commit_message_guide` and
   `workflow="project"`. The tool's answer says what each message contains.
   [references/static-quality.md](references/static-quality.md) says where you
   split a formatting pass into its own commit, and in which order the commits
   go.

## Where this skill stops

Hand the work to the skill that covers it at the point you verified. Stop before
you edit its files, and keep only the testing part.

- **A broad conformance audit** is `typo3-extension-health`.
- **A documentation rewrite** is `typo3-extension-documentation`.
- **A backend module** is `typo3-backend-module-development`.
- **A content element** is `typo3-content-element-development`.
