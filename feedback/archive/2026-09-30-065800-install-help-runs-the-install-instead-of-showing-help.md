---
date: 2026-09-30T06:58:00+00:00
category: bug
status: closed
closed: 2026-10-05
model: claude-opus-5-5
tool: bin/typo3-dev-companion install
directory: /home/lina/projects/typo3/tools/dev-companion
---

# `install --help` runs the install instead of showing help

## Observation

Task: install the companion into /home/lina/projects/bzga (a directory of
TYPO3 site projects). Before running it there I wanted to see the options,
so I ran, from the companion's own checkout:

    php bin/typo3-dev-companion install --help

No help was printed. The command performed a full install into the current
directory, which was the companion checkout itself: it reused the existing
.mcp.json, wrote the "TYPO3 Dev Companion" block into the checkout's tracked
AGENTS.md and published all skills to .agents/skills. The only visible sign
was the usual "Configured/Wrote/Published" output. I had to notice the
modified AGENTS.md via git status and revert it.

`install` writes into whatever directory it runs in, so an option that is
universally read as "show usage, do nothing" turning into a write is the
riskiest possible reading. In a site repository it would have left untracked
files (.mcp.json, .agents/) and an edited AGENTS.md behind.

## Query

php bin/typo3-dev-companion install --help, run from the root of the
companion checkout.

## Suggestion

Make `--help` / `-h` on `install` (and on every subcommand) print usage
without side effects, including the target directory it would write to and
the files it would touch. Unknown options should abort with an error instead
of being ignored. Optionally refuse, or ask, when the target directory is the
companion checkout itself, and offer a `--dry-run` that lists what would be
written.
