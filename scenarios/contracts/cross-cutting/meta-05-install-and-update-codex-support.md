# META-05 — Install and update Codex support

**Environment:** `E-SITE`, with Codex project configuration containing unrelated
settings · **Contract:** `held` — `R-DIS-012`
**Held by:**
`InstallerTest::codexInstallAndUpdateTrackTheirSkillsCentrally`,
`InstallerTest::codexInstallRefusesAConflictingServerEntry`,
`InstallerTest::codexUpdateReplacesAModifiedGeneratedSkill`

> Set this TYPO3 assistant up for Codex in this project. Keep everything already
> configured. Then update the generated integration once so I know future
> releases can refresh it safely.

**What the agent needs from this server**

- One explicit install path for the Codex MCP configuration and task skills.
- An idempotent update path that knows which generated files belong to this
  package.
- A conflict result that leaves existing foreign or user-modified content
  untouched.

**What has to come out of it**

- Unrelated Codex configuration survives byte-for-byte in meaning. The installer
  reuses a server entry that matches, and declines a different one.
- Repeating install or update produces no duplicate entry and no unnecessary
  file change.
- The installer reports a generated file the user modified rather than replaces
  it silently.

**How it fails**

- The installer regenerates the whole Codex configuration from a template.
- Update overwrites a file whose current content is no longer the version this
  package generated.
- The MCP entry lands but the task skill stays stale.
