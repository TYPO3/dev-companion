---
id: D-DIS-037
title: A stopped DDEV project answers with an error and starts nothing
date: 2026-11-06
status: open
coveredBy:
  - DdevLauncherTest::aRunningProjectHandsTheServerTheClientsFirstRequest
  - DdevLauncherTest::aStoppedProjectAnswersEveryRequestWithTheCommandOnACleanStream
---

# D-DIS-037 — A stopped DDEV project answers with an error and starts nothing

**The launch entry reads the project's state. On a stopped project it writes a
JSON-RPC error that names `ddev start`, and it starts nothing.**

## Evidence

- `feedback/archive/2026-11-05-180136-a-cold-ddev-exec-launch-writes-ddev-s-own.md`
  measured 292 bytes of DDEV's output on stdout, and then either exit 0 or a
  hang on identical input.
- `feedback/2026-10-31-073718-a-server-launched-through-ddev-exec-cannot.md`
  reported the same entry from the symptom, and keeps the half that is open: a
  silence with the project running.
- On 2026-11-06, DDEV v1.25.5 on Linux: `ddev describe -j` answers in about a
  second with JSON on stdout, starts nothing, and reports `status_desc`. On a
  stopped project the launcher wrote only JSON-RPC errors, and the project
  stayed stopped. On a running one the server read the client's first request.
- Not measured: Windows and macOS, and an idle project. `ddev pause` is no
  command in this version. The launcher reads anything but `running` as not
  running.
- Windows keeps `ddev exec php`, so the defect stands there.

## Decided

- **The maintainer chose this shape on 2026-11-06.** A wrapper that starts the
  project is measured and works, and it brings a container up without consent. A
  sentence in the install output leaves the entry broken.

## Assumed

- That a client shows the person the error text. Claude Code 2.1.319 handed it
  to the session word for word. A Codex run on 2026-11-06 printed no line that
  named the server, and that run did not establish whether Codex started it at
  all.

## Wrong if

- A client treats that error as a dead server and offers the person no message.
