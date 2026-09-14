---
id: D-DIS-016
title: How an entrypoint may be named is a per-client question
date: 2026-08-08
status: confirmed
coveredBy:
  - InstallerTest::aCheckoutElsewhereKeepsTheHostPath
  - InstallerTest::aClientResolvingTheRootGetsAnEntryThatIsRightAnywhere
  - InstallerTest::aDependencyOfTheProjectStillNamesTheHostPath
---

# D-DIS-016 — How an entrypoint may be named is a per-client question

**Whether an entry may name this server by a path relative to the project is a
per-client question, and that client's own documentation answers it.** A client
that does not answer stands recorded as unestablished rather than assumed.

`D-DIS-015` wanted one rule for all eleven targets. The clients give at least
three answers, and the one this repository runs with most warns against the rule
it proposed.

## Evidence

- The MCP specification defines the stdio transport as "the client launches the
  MCP server as a subprocess" and says nothing about a work directory. So
  nothing at the protocol level makes a relative `args` entry resolvable.
- Read 2026-08-08, four of the eleven:
  - **VS Code** — a `cwd` field, "Working directory for the server command.
    Defaults to the workspace folder when run in a workspace." Relative is safe
    and documented, and `${workspaceFolder}` is available besides.
  - **opencode** — a `cwd` option, "Working directory for the MCP server
    process. Relative paths resolve from the workspace." No variables
    documented.
  - **Cursor** — no documentation of the work directory. Variables are: "Cursor
    resolves variables in these fields: `command`, `args`, `env`, `url`, and
    `headers`", `${workspaceFolder}` among them.
  - **Claude Code** — no documentation of the work directory, and the opposite
    advice. It sets `CLAUDE_PROJECT_DIR` in the spawned server's environment "so
    your server can resolve project-relative paths without depending on the
    working directory". `${VAR}` expands in `command` and `args`, but that
    variable "is set in the server's environment, not in Claude Code's own". So
    a `.mcp.json` reference "requires a default such as
    `${CLAUDE_PROJECT_DIR:-.}`", and the default is the work directory again.
- The seven this entry did not read stand unestablished, not assumed either way.
- This is why the DDEV branch is correct where a general one would not be.
  `ddev exec` runs in the container's project root, so DDEV supplies the work
  directory the client does not promise.

## Decided

- No blanket rule. The shape of a shareable entry is a property of each client,
  the way `D-DIS-009` found the restart-and-approval answer to be.
  `documentation/usage/installing.rst` is where a per-client answer lands.
- Three shapes are available and the documentation decides which one applies. A
  plain relative path where the client documents the workspace as the default
  work directory. A variable the client resolves to the project root. Or the
  absolute path with an install line that says it is machine-specific.
- The absolute path stays until a client's own documentation says otherwise. It
  is wrong on another machine and right on this one; a relative path against an
  undocumented work directory would be wrong on both.
- Saying it is the floor and is right under every answer. That is the same
  sentence the standalone card carries as its first option. So the two halves of
  the reported defect converge on one mechanism rather than two.

## Assumed

- That the four readings describe the clients as shipped. Each is the client's
  current documentation and none was driven — this repository can install into a
  client but cannot watch one spawn a process.

## Wrong if

- A client documents a workspace work directory and still spawns elsewhere,
  which would make the documented half of this as unreliable as the undocumented
  half.
- The per-client shapes turn out to be one shape after a read of the other
  seven. The split cost more than a single rule would have.
- The machine-specific path stays the answer everywhere, and the read bought a
  sentence that `install` could have printed without it.

## Confirmed on 2026-08-09

The other seven had their read and the split held. None documents a work
directory for a stdio server, and none a variable that reaches the project root
in `command` or `args`. So two of the three shapes are in use and the second
**Wrong if** did not fire. That is two clients of eleven, a narrow return for a
read of all of them and still the return. Without it the third **Wrong if**
would have fired instead. The plain relative path goes to nobody, because the
variable says the same thing and rests on nothing about where the client started
the process. One assumption joins: that the workspace variable is the directory
`install` ran in, which parts from it only in a multi-root workspace.
