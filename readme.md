# TYPO3 Dev Companion

> **Experimental.** This is a 0.x package and its surface is not settled. The
> package name, the binary, the namespace and the environment variables all
> changed in one cut on 2026-08-06. The tool names and the shapes they answer in
> can move the same way. Pin a commit where you depend on it.

A local MCP server (plain PHP) that helps coding agents implement, review and
verify TYPO3 work for the three audiences that do it: the core contributor, the
extension author and the site developer. It establishes the project and
installation the agent is working in, supplies current, version-bound TYPO3
knowledge, and hands task-specific workflows to the skills that own them.

It answers for TYPO3 **12.4**, **13.4**, **14.3** and **main**. A statement that
does not hold on all of them names the ones it does, so an LTS is never handed a
convention that only the development line has.

The MCP client launches it as a subprocess over stdio, so there is no server to
host, no network exposure and no auth to configure — the process boundary is the
trust boundary. It reads: nothing is written into the TYPO3 installation it is
pointed at, and nothing on the machine is started as a side effect of a lookup.

## Quickstart

Requirements: **PHP 8.2+** and Composer. One standalone checkout serves every
project on the machine and stays out of each project's dependencies:

```bash
git clone https://github.com/TYPO3/dev-companion.git typo3-dev-companion
composer install --working-dir=typo3-dev-companion
cd /path/to/your/project
/absolute/path/to/typo3-dev-companion/bin/typo3-dev-companion install
```

`install` writes into the directory it is run in, so the last two lines are run
from the root of the project the agent works in. A project can require the
package instead, `composer require "typo3/dev-companion:@dev"`, and run
`vendor/bin/typo3-dev-companion install`; the manual has that case.

`install` writes the `typo3-dev-companion` entry into the project's `.mcp.json`
and publishes the task skills to `.agents/skills` — the two locations a client
finds without being configured for it. `--agent=<id>` writes them where that
client actually reads them instead.

It is also the only supported way to get the skills: a copy of `skills/` taken
out of this repository by hand is missing the `references/base.md` the installer
writes into each of them, and every skill opens on that file.

## Documentation

The manual reads as a site at
[typo3.github.io/dev-companion](https://typo3.github.io/dev-companion/),
and as files in [documentation/](documentation/readme.rst):

- [Usage](documentation/usage/readme.rst) — the path from the install to the
  first answered question, and behind it every client the install supports and
  the task workflows it publishes.
- [Server](documentation/server/readme.rst) — what it can be asked, one page per
  tool, and where each answer comes from.
- [Contributing](documentation/contributing/readme.rst) — working on this
  repository rather than using it.
- [Records](documentation/records/readme.rst) — how feedback from a real session
  becomes an improvement.

The conventions every session here works under are [AGENTS.md](AGENTS.md).
