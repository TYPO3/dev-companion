# SKILL-01 — Find the missing tests before adding them

**Environment:** `E-SITE`, in the project's site package · **Contract:** `held`
**Held by:**
`SkillTest::extensionTestingVerifiesItsHarnessBeforeAddingCoverage`,
`SkillTest::judgmentHeavySkillsKeepTheirChecklistBesideThem`,
`ScopeTest::noRunTestsCommandIsHandedToARepositoryThatHasNoRunTests`

> Review our site package's test coverage. Tell me which important
> behaviors are not protected, choose the right test layer for each gap, and
> give me the exact commands we should run. Do not change files yet.

**What has to come out of it**

- The answer inspects production code, nearby tests, configuration, and declared
  project commands before recommending work.
- Each proposed test names a concrete unprotected behavior and explains why
  unit, functional, browser, or architecture coverage is the lowest useful
  layer.
- Commands exist in the checkout or the project declares them. The answer offers
  no TYPO3 core `runTests.sh` suite.
- Missing infrastructure and commands not executed are clearly marked.

**How it fails**

- A generic testing pyramid without file-level evidence.
- Invented PHPUnit paths, Composer scripts, or core-only commands.
- Treating a file-presence assertion as proof of runtime behavior.
