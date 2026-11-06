---
id: D-DIS-037
title: A stopped DDEV project answers with an error and starts nothing
date: 2026-11-06
status: open
coveredBy: []
---

# D-DIS-037 — A stopped DDEV project answers with an error and starts nothing

**The launch entry reads the project's state. On a stopped project it writes a
JSON-RPC error that names `ddev start`, and it starts nothing.**

## Evidence

- `feedback/2026-11-05-180136-a-cold-ddev-exec-launch-writes-ddev-s-own.md`
  measured 292 bytes of DDEV's output on stdout, and then either exit 0 or a
  hang on identical input.
- `feedback/2026-10-31-073718-a-server-launched-through-ddev-exec-cannot.md`
  reports the same entry from the symptom.

## Decided

- **The maintainer chose this shape on 2026-11-06.** A wrapper that starts the
  project is measured and works, and it brings a container up without consent. A
  sentence in the install output leaves the entry broken.

## Assumed

- That an entry can read the state without writing to stdout on every platform
  `install` serves. Nobody has measured it, and `T-261105-6e1f` starts there.

## Wrong if

- A client treats that error as a dead server and offers the person no message.
