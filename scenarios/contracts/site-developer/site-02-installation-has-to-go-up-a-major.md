# SITE-02 — The installation has to go up a major

**Environment:** `E-SITE` on the previous major · **Contract:** `held` —
`R-PRJ-004`: `typo3_task_guide` recognises the task and composes the order of
operations with what `typo3_project_describe` and `typo3_changelog_lookup` know
**Held by:**
`HintsTest::upgradingAnInstallationIsAnsweredAsAnOrderOfOperations`,
`PackageSourcesTest::theChangelogIsNarrowedByTypeAndVersion`,
`ProjectTest::theProjectIsDescribedFromItsFilesAlone`

> This site is on the previous LTS and we need to be on the current one before
> support ends. Plan the upgrade, tell me what will break, and start with the
> parts that are safe.

**What the agent needs from this server**

- The order of operations: dependencies, the upgrade wizards, the extension
  scanner, the caches, what has to run in which environment.
- How to find out what breaks — the scanner, the changelog, the deprecation
  notices in the log.
- What the third-party extensions in this installation do to the plan, and which
  ones are actually installed here.
- The effective configuration as it is now, so the agent can compare what the
  upgrade changes against something real.

**What has to come out of it**

- The parts that are properties of this installation come from it rather than
  from an assumption.
- The upgrade procedure itself gets an answer or a refusal. What it must not get
  is a guess. A wrong order here costs a production site.

**How it fails**

- An upgrade plan assembled from the model's own memory of TYPO3 majors,
  indistinguishable in tone from the parts that came from the knowledge base.
- Version-specific claims with no branch named (`R-AUD-004`).
