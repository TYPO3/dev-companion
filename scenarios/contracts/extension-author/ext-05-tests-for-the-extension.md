# EXT-05 — Tests for the extension

**Environment:** `E-EXT` · **Contract:** `held` — `R-KNW-015`,
`R-SKL-002`
**Held by:**
`SkillTest::extensionTestingVerifiesItsHarnessBeforeAddingCoverage`,
`SkillTest::extensionTestingLoadsOnlyTheSelectedLayerGuide`,
`ScopeTest::noRunTestsCommandIsHandedToARepositoryThatHasNoRunTests`

> Set up tests for this extension — unit and functional — and wire them into our
> GitHub Actions so every pull request runs them against all supported TYPO3
> versions.

**What the agent needs from this server**

- Testing conventions that transfer: what belongs in a unit test, what needs a
  functional one, how to keep fixtures deterministic, and where tests sit.
- A workflow that inspects and proves the existing harness before changing it,
  then establishes or repairs only the layers the extension actually needs.
- The extension-test configuration, bootstrap and database requirements that
  exist outside the core repository. Plus versioned documentation where the
  installed framework or CI matrix has to decide.

**What has to come out of it**

- The tests, configuration, dependency constraints and commands that exist
  survive and grow instead of a parallel harness that replaces them.
- Unit and functional commands run locally before CI calls those same commands.
  Dependency versions and matrix combinations come from the package rather than
  from a copy of another extension.
- A meaningful test or an honestly empty discovered suite proves every
  established layer. The answer offers no core `runTests.sh` command and no
  vacuous green test.
- Functional database prerequisites are separate from assertion failures, and
  credentials are not committed.

**How it fails**

- A core suite or copied configuration that cannot run in the extension.
- CI YAML written before its local command has passed.
- Tests or scripts that work replaced because the agent treated the task as a
  blank project.
- `assertTrue(true)` presented as proof that the harness exercises the
  extension.
