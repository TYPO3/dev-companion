---
date: 2026-10-31T07:37:18+00:00
category: bug
status: open
model: claude-opus-5
directory: /home/lina/projects/typo3/tools/dev-companion
---

# a server launched through ddev exec cannot report its own stopped project

## Observation

A session in this checkout could not reach this server for its whole length.
The client reported:

```
MCP server typo3-dev-companion connection timed out after 30000ms
```

The cause was a cold DDEV project. `ddev poweroff` ran the evening before, so
the first `ddev exec` had to bring the containers up, and a DDEV start takes
longer than the client waits.

**The same command answered in two seconds once the project was warm.** The
handshake returned the full `initialize` result with no stderr. So nothing was
wrong with the server, the entry, or the installation.

## Why the entry is right and still fails

`Installer::startedBy()` takes `['ddev', 'exec', 'php', <entrypoint>]` wherever
the project has a `.ddev/config.yaml`. Its own docblock gives the reason, and
the reason holds. The container's PHP sees the project directory rather than the
host. The entry carries a path the project can share, so the file stays
committable, which `HOST_SPECIFIC` exists to avoid having to say.

So the defect is not the choice. The defect is that this form has a state in
which it cannot work, and nothing anywhere names it.

## What makes it hard to diagnose

The client's message names a timeout. It does not name DDEV, the project, or
its state. A reader with a working installation and a correct entry has no
thread to pull. This session spent its diagnosis on the server first, and found
it healthy, because a plain `php` handshake from the same directory answers at
once. The trial projects on the same machine use the plain form, so they kept
working, which pointed further away rather than closer.

## Why the server cannot answer this one itself

`AGENTS.md` already states the posture: the server reports a stopped DDEV
project with the command that would fix it, and never starts something on the
caller's machine as a side effect. That rule covers every answer the server
gives.

It cannot cover this. The project has to be up before the server runs at all,
so there is no answer in which to report it. The only place a statement can
stand is the output of the run that writes the entry.

## Suggestion

Judge whether the install run says it where it chose the DDEV form. One
sentence, beside the entry it just wrote: this entry starts the project's
container, so a stopped project reads as a connection timeout.

Weigh that against the alternative of naming the state in `documentation/`
alone, which the person who meets the timeout a month later does not read.

Do not make the entry start the project. That is the side effect `AGENTS.md`
bars, and a client that launches a server has no consent to bring a container
up.
