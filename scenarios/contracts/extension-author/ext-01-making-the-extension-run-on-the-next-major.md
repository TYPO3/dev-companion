# EXT-01 — Making the extension run on the next major

**Environment:** `E-EXT` · **Contract:** `held` — the multi-major upgrade
workflow exists since 2026-07-31 and owns the order this case asks for
**Held by:** `ScopeTest::noCoreScriptIsHandedToARepositoryThatDoesNotHaveIt`,
`PackageSourcesTest::theChangelogOfTheInstalledCoreIsSearchable`,
`SkillTest::anUpgradeIsOrderedWorkAndStopsWhereAnotherSkillStarts`,
`SkillTest::theDeprecationSweepRunsFromTheExtensionsSurface`;
that a session works in that order is `not guarded`, and by `D-EVI-002`'s
reasoning no forward run will hold it — the wording is the proxy

**Read 2026-09-02:** `typo3-extension-upgrade` still owns the order this case
asks for: it starts from the base's deprecation sweep rather than restating it,
widens that sweep into the work list, and stops before the work another skill
owns. Its description and body were rewritten on 2026-08-18 so a defect inside
the declared range meets the premise — `D-SKL-061`. The order a session works in
is unguarded as before.

> Our extension supports TYPO3 12 and 13. The next major is out and I want to add
> support for it without dropping 13. Go through the extension, find what breaks,
> and fix it.

**What the agent needs from this server**

- How to find out what actually changed: the changelog directory, the extension
  scanner, the deprecation annotations — a procedure that works on any branch,
  not a list from one.
- Which conventions the new code has to follow where it is rewritten: DI, event
  listeners instead of hooks, the TCA and schema shape, the Fluid changes.
- What supporting two majors at once does to `composer.json`, `ext_emconf.php`
  and the code that has to branch.

**What has to come out of it**

- The conventions that transfer come over and are marked as conventions rather
  than as core rules; nothing that only exists in the core repository is handed
  to a repository that does not have it.
- Where the server has no upgrade knowledge, the answer says so and points at
  the documentation instead of producing a checklist that reads authoritative.
- The version-specific part is a procedure to run against the checkout.

**How it fails**

- `Build/Scripts/runTests.sh` offered to a repository that has no `Build/`
  (`R-SCO-002`).
- A core patch checklist — Gerrit, Forge, `Releases:` — handed to an extension
  release (`R-AUD-001`).
- A list of "what changed in the new major" invented from memory, presented as
  fact.
