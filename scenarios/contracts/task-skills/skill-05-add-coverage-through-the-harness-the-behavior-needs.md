# SKILL-05 — Add coverage through the harness the behavior needs

**Environment:** `E-EXT`, in an extension that has unit tests but no working
functional suite · **Contract:** `held`
**Held by:**
`SkillTest::extensionTestingVerifiesItsHarnessBeforeAddingCoverage`,
`SkillTest::extensionTestingLoadsOnlyTheSelectedLayerGuide`

> Our extension already has some unit tests. Add coverage for the repository's
> persistence behavior and make whatever test setup that requires work locally
> and in CI. Preserve the tests and commands we already have.

**What has to come out of it**

- The answer inspects and runs or diagnoses the existing unit harness before
  changing test infrastructure.
- Persistence goes to a functional test. The absent functional harness comes
  from the package constraints, installed testing framework and versioned
  documentation. It does not come from a guess or from mocks in its place.
- Existing unit configuration and commands survive; one stable local functional
  command passes before CI calls the same command.
- Database prerequisites come from the declared environment, credentials stay
  untracked, and the answer does not report an infrastructure failure as a
  failed persistence assertion.
- The new test exercises observable repository behavior with deterministic
  fixtures. No core `runTests.sh` command or vacuous assertion appears.

**How it fails**

- The repository receives another unit test because that runner already exists.
- Setup becomes a competing test tree or replaces working scripts.
- A CI matrix copied from another extension without a resolution of this
  package's supported constraints.
- Configuration files exist, but no local functional test is actually run.
