# Playwright browser guidance

Read this after you choose a browser layer. Keep the runner with the project
that serves the TYPO3 site. Let the checkout and the current official Playwright
documentation decide dependency versions, supported configuration fields, and
browser installation commands.

## Verify the harness

1. Inspect the harness before you touch it. That is the project and frontend
   package manifests, the lock file, the Playwright configuration, and the test
   directories. It is the scripts, the ignored artifacts, and CI. Inspect the
   command that serves the site.
2. Establish the real mounted URL and whether the journey needs backend or
   frontend authentication. Do not invent a page identifier. Do not certify an
   unmounted content element.
3. Run test discovery and one existing spec before you edit the harness. Tell
   runner installation, browser availability, site reachability, authentication,
   and assertion failures apart.
4. Choose where browsers execute from evidence in the project: host, development
   container, or dedicated browser image. Keep the site URL and the artifact
   paths reachable from that execution environment.
5. If the checkout does not decide browser execution, leave the topology open.
   Local and CI reachability, browser persistence, and the project's container
   policy decide it later. Do not turn a generic host or DDEV preference into
   project evidence.

## Choose the folders

Keep an established repository layout. Where none exists, `typo3_rule_lookup`
with `documentId="project/testing/playwright"` holds the files, whole. Those are
`Build/playwright.config.ts`, the login setup and a spec per project. The page
adds the environment they read the site from, and what you do not commit.

- Project-owned describes the suite's lifecycle, not necessarily the repository
  root. Reuse an existing frontend package manifest when it belongs to this
  deployed project and can own browser commands. Do not create a second manifest
  only to move Playwright closer to the root. If the only manifest belongs to a
  reusable extension, establish an explicit project test package instead.
- Point `testDir` at the chosen browser-test root. Do not rely on accidental
  discovery across PHP or frontend unit tests.
- Keep setup files distinct from journey specs. Then express their order through
  Playwright project dependencies.
- Keep reusable browser fixtures and page objects apart from test cases only
  after a second spec needs them.
- Store accepted visual baselines beside their specs or under an explicit
  snapshot path. Commit only those baselines.
- If the repository already uses `Tests/Browser/`, `e2e/`, or a frontend-package
  directory, keep it and map the same ownership boundaries there.

## Establish or repair it

- Put configuration and specs at the project level unless the repository already
  owns them elsewhere. A standalone extension cannot prove a rendered journey
  without a site that mounts it.
- Read the base URL from the environment or the project configuration. Do not
  commit a developer-specific host.
- Add stable package scripts for a targeted run, the normal suite, and any
  accepted snapshot-update workflow before you wire CI.
- Keep reports, traces, screenshots, videos, and temporary authentication state
  in declared artifact or ignored paths. Commit reference snapshots only when
  the project uses visual regression tests on purpose.
- Use setup dependencies or fixtures for shared authenticated state. Do not log
  in on its own in every spec.
- Enable only the browsers and projects the task or the support policy requires.
  More combinations are not evidence when nobody runs or maintains them.

## Write the spec

- Test a user-visible journey or a browser-only contract. That is navigation,
  authentication, form behavior, JavaScript interaction, focus, responsive
  behavior, visual output, or accessibility.
- Prefer role, label, and other user-facing locators over DOM structure or CSS
  implementation details.
- Wait for observable conditions rather than fixed timeouts.
- Keep test data deterministic. Clean up the state the spec creates.
- Add an accessibility scan to the relevant mounted page or journey when the
  task makes an accessibility claim. Do not disable a rule merely to make the
  first run green.
- Review visual differences before you update snapshots. Never accept baselines
  blindly.

## Prove it

1. Run the new spec alone against the real served site.
2. Confirm that the expected report, trace, screenshot, or snapshot path works.
3. Run the containing browser project or suite.
4. Run the same script CI will call.
5. Report untested browsers, unavailable URLs, and environment prerequisites as
   unverified rather than passed.
