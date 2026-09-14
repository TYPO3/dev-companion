---
id: R-DIS-012
title: 'Codex setup installs the server and its skills'
status: held
heldBy:
  - InstallerTest
---

# R-DIS-012 — Codex setup installs the server and its skills

**Codex setup installs both the MCP entry and the task skills through an
explicit agent option.**

An update replaces its complete generated skill directories and preserves
unrelated skills and configuration. It reports a server entry in conflict rather
than replaces it. Repeated install and update calls are idempotent. The
installer adds the central generated state and only the package-owned skill
directories to `.gitignore`; merged MCP and agent configuration stays
versionable. In a DDEV project the generated client entry runs the Composer
binary through DDEV, while the skills go into the host-mounted project.

## From

`META-05`.
