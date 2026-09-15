# SKILL-08 — Establish static quality beside the checks that exist

**Environment:** `E-EXT`, in an extension that runs a code style fixer and a
syntax lint but has no static analyser, no baseline and no test suite ·
**Contract:** `held`
**Held by:**
`SkillTest::extensionTestingKeepsCheckingApartFromFixing`,
`SkillTest::everyReferenceIsOneHopAwayAndLoadedOnDemand`

> Set up PHPStan and CGL for this TYPO3 extension, fix the findings in the
> changed code, and make the same checks run locally and in CI without replacing
> the quality commands we already have.

**What has to come out of it**

- The answer establishes the absent analysis rather than declines it because the
  project does not run one yet. It names what a complete check surface covers
  before it says what this project lacks.
- The fixer, lint step and their configuration that exist survive and run before
  any change. What comes in extends them instead of a parallel set of commands.
- Development dependencies resolve against this package's declared TYPO3 and PHP
  range, and the solver accepts them before a constraint lands.
- Each check gets one project-owned command, the command that reports is
  separate from the command that writes, and CI calls the commands that passed
  locally.
- Findings in the changed code get their fix. A baseline, if one exists at all,
  holds what was already there and carries the name of a work list with a
  horizon.
- Automatic formatting stays inside the extension's own files, and the answer
  reports which files the fixer changed.

**How it fails**

- Static analysis reported as out of scope, or handed back as a testing
  question, because the extension does not use it yet.
- The analyser pointed at the installed core, the vendor tree or generated
  output, so its report is about code the extension does not own.
- New findings written into a baseline, or the analysis level set where the
  report is empty.
- One command both checks and rewrites, so CI cannot call it and the working
  tree changes during a review.
- The fixer runs over vendored, generated or third-party files, and the answer
  does not say which files it touched.
