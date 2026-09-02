# Playwright browser guidance

Read this after choosing a browser layer. Keep the runner with the project that
serves the TYPO3 site. Let the checkout and current official Playwright
documentation decide dependency versions, supported configuration fields, and
browser installation commands.

## Verify the harness

1. Inspect the project and frontend package manifests, lock file, Playwright
   configuration, test directories, scripts, ignored artifacts, CI, and the
   command that serves the site.
2. Establish the real mounted URL and whether the journey needs backend or
   frontend authentication. Do not invent a page identifier or certify an
   unmounted content element.
3. Run test discovery and one existing spec before editing the harness.
   Distinguish runner installation, browser availability, site reachability,
   authentication, and assertion failures.
4. Choose where browsers execute from evidence in the project: host, development
   container, or dedicated browser image. Keep the site URL and artifact paths
   reachable from that execution environment.
5. If the checkout does not decide browser execution, leave the topology
   unresolved until local and CI reachability, browser persistence, and the
   project's container policy decide it. Do not turn a generic host or DDEV
   preference into project evidence.

## Choose the folders

Keep an established repository layout. Where none exists, `typo3_rule_lookup`
with `documentId="project/testing/playwright"` is the files, whole:
`Build/playwright.config.ts`, the login setup, a spec per project, the
environment they read the site from, and what is not committed.

- Project-owned describes the suite's lifecycle, not necessarily the repository
  root. Reuse an existing frontend package manifest when it belongs to this
  deployed project and can own browser commands; do not create a second manifest
  only to move Playwright closer to the root. If the only manifest belongs to a
  reusable extension, establish an explicit project test package instead.
- Point `testDir` at the chosen browser-test root instead of relying on
  accidental discovery across PHP or frontend unit tests.
- Keep setup files distinct from journey specs, then express their order through
  Playwright project dependencies.
- Keep reusable browser fixtures and page objects separate from test cases only
  after a second spec needs them.
- Store accepted visual baselines beside their owning specs or under an explicit
  snapshot path and commit only those baselines.
- If the repository already uses `Tests/Browser/`, `e2e/`, or a frontend-package
  directory, retain it and map the same ownership boundaries there.

## Establish or repair it

- Put configuration and specs at the project level unless the repository already
  owns them elsewhere. A standalone extension cannot prove a rendered journey
  without a site that mounts it.
- Read the base URL from environment or project configuration rather than
  committing a developer-specific host.
- Add stable package scripts for a targeted run, the normal suite, and any
  accepted snapshot-update workflow before wiring CI.
- Keep reports, traces, screenshots, videos, and temporary authentication state
  in declared artifact or ignored paths. Commit reference snapshots only when
  the project deliberately uses visual regression testing.
- Use setup dependencies or fixtures for shared authenticated state instead of
  logging in independently in every spec.
- Enable only the browsers and projects the task or support policy requires.
  More combinations are not evidence when nobody runs or maintains them.

## Write the spec

- Test a user-visible journey or browser-only contract: navigation,
  authentication, form behavior, JavaScript interaction, focus, responsive
  behavior, visual output, or accessibility.
- Prefer role, label, and other user-facing locators over DOM structure or CSS
  implementation details.
- Wait for observable conditions rather than fixed timeouts.
- Keep test data deterministic and clean up state the spec creates.
- Add an accessibility scan to the relevant mounted page or journey when the
  task makes an accessibility claim. Do not disable a rule merely to make the
  first run green.
- Review visual differences before updating snapshots; never accept baselines
  blindly.

## Prove it

1. Run the new spec alone against the real served site.
2. Confirm the expected report, trace, screenshot, or snapshot path works.
3. Run the containing browser project or suite.
4. Run the same script CI will call.
5. Report untested browsers, unavailable URLs, and environment prerequisites as
   unverified rather than passed.
