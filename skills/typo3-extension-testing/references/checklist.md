# Test strategy checklist

Read this when you choose test layers, set up or repair their harnesses, review
coverage, or select commands. Use only the rows the task and the checkout
support.

## Verify the starting point

- Identify the layers the requested behavior needs.
- Run or inspect each layer's discovery command before you rely on it.
- Establish or repair missing prerequisites before you add coverage.
- Preserve a review-only boundary: report setup defects without changing them.
- Do not force behavior into an existing lower layer merely because the correct
  layer has no harness yet.

## Select the layer

- Unit: isolated logic whose behavior needs neither TYPO3 bootstrap nor
  persistence.
- Functional: services, configuration, database schema, repositories,
  DataHandler, fixtures, or interaction between TYPO3 components.
- Browser: rendered journeys, backend interaction, JavaScript, or accessibility
  behavior nothing below the UI can establish.
- Static quality: defects and style no test observes — static analysis, coding
  standards, structural rules. Establish them when the task asks for them.
  Extend them rather than invent them when it does not.

## Respect the functional frontend boundary

- A TYPO3 functional frontend subrequest proves server-side routing, rendering
  and the generated response.
- An asset URL or AssetCollector tag in that response proves registration, not
  that the browser loaded or executed the asset.
- A functional test does not execute JavaScript, apply CSS, calculate layout,
  exercise focus behavior or inspect the browser accessibility tree.
- Use a browser test for an interactive claim. Describe untested interaction as
  unverified rather than as frontend-tested.
- Documentation and configuration: validate examples and declarations directly.
  Do not disguise a file-presence assertion as a behavioral test.

Prefer the lowest layer that observes the contract and would fail for the
reported defect. Record why a higher layer is necessary.

## Complete setup

- Resolve development dependencies against the package's declared TYPO3 and PHP
  constraints. Do not copy a version from another project.
- Reuse installed template configuration and existing project conventions.
- Keep extension-owned unit and functional tests in the extension. Keep
  browser-owned configuration in the project that serves the tested site.
- Establish a stable local command before CI, then make CI call that command.
- Keep a check command apart from the command that writes. Keep automatic
  formatting inside the first-party paths the project intends it to touch.
- Fix a new static-analysis finding. A baseline records what was already there.
- Keep credentials and machine-specific URLs out of tracked configuration.
- Prove every newly established layer with a meaningful test or a real browser
  spec. When no unit-testable behavior exists, report an empty discovered suite.
  Do not manufacture behavior or a vacuous assertion.
- Treat a missing runner or environment as an infrastructure failure, not as a
  failed product assertion.

## Select commands

1. Prefer the narrowest existing command that names the affected test or suite.
2. During setup, introduce one project-owned command per selected layer. Run it
   before you wire CI.
3. Use a direct PHPUnit or browser-runner invocation only when its executable,
   configuration, and target path exist in the checkout.
4. Then run the containing command `typo3_project_describe` declares, or the
   repository's documented test setup.
5. Never translate TYPO3 core commands into extension commands by analogy.
6. Separate missing infrastructure or environment prerequisites from a failing
   test.

## Coverage review

- Trace every claimed behavior to an existing test or a concrete missing case.
- Include success, boundary, failure, authorization, and persistence behavior
  only where the production code exposes those branches.
- Check that fixtures are minimal, deterministic, and independent of local site
  data, time, execution order, and external services.
- Name exact files and commands. Label anything you did not execute as
  unverified.
