---
date: 2026-11-05T18:01:36+00:00
category: tool-gap
status: open
model: claude-opus-5
tool: typo3-dev-companion, install
directory: /home/lina/projects/typo3/tools/dev-companion
---

# a cold ddev exec launch writes DDEV's own output onto the protocol stream

## Observation

`install` writes `ddev exec php <entrypoint>` as the launch command wherever the project has a `.ddev/config.yaml`. `Installer::startedBy()` decides that. The wrapper is not transparent, so the entry fails on a project that is stopped.

Measured on 2026-11-05 in a fresh project. `ddev config --project-type=typo3 --docroot=public`, then `ddev composer create-project "typo3/cms-base-distribution:^14"` for TYPO3 14.3.10, then `composer require --dev typo3/dev-companion:@dev` for dev-main af25c0f. `install` wrote the entry above.

A client that launches that entry on a stopped project receives 292 bytes on stdout, and none of it is JSON-RPC:

```
Generating additional.php file for database connection.
Building project images...Project images built in 2s.
Waiting for containers to become ready: [web db]... ready in 4.1s
ddev-router already running, pushing new config...
Generating additional.php file for database connection.
```

Every line is DDEV starting the project. `Generating additional.php` is DDEV's own TYPO3 hook. The server's own warning about a missing `settings.php` goes to stderr, which is correct.

The process then ends in one of two ways on identical input. One run answered exit 0 after 18 seconds. The next hung until a 120 second timeout killed it. So the failure is a race rather than a rule, and a client sees either a refused handshake or no answer at all.

`ddev exec -q` writes the same 292 bytes. Its own help says it suppresses error detail, so it does not reach this.

Warm, the identical command is correct. It answers 6263 bytes with `{` as the first byte.

This repository's own `.mcp.json` carries that entry. On 2026-11-04 two `typo3_feedback_record` calls from a session here hung for about 1800 seconds and wrote nothing. That session recorded its two feedbacks over a direct stdio connection with plain `php` instead. So the defect already reached the work on this server.

One trap sits in front of any repair. A first wrapper ran `ddev start` before the server and left stdin attached to it. `ddev start` consumed the client's `initialize` request. The result was zero bytes of stdout, exit 0, and a silent dead handshake, which reads like a server that answers nothing.

## Query

A cold project, with the entry `install` writes. `printf '<initialize request>' | ddev exec php vendor/bin/typo3-dev-companion`, run after `ddev stop`. Then the same with `-q`, and the same warm. Then `printf '<initialize request>' | sh -c 'ddev start -y >/dev/null 2>&1 </dev/null; exec ddev exec php vendor/bin/typo3-dev-companion'`.

## Suggestion

Whatever the repair, stdout carries the protocol and nothing else. That is the part no reading of the rules disputes.

Two repairs answer it, and they disagree with each other.

The wrapper works, and this session verified it. `sh -c 'ddev start -y >/dev/null 2>&1 </dev/null; exec ddev exec php <entrypoint>'` answers a cold project correctly in 22 seconds, 6263 bytes, `{` first. It must withhold stdin from the start step, for the reason the observation gives.

`AGENTS.md` argues against it: "Never start something on the caller's machine as a side effect of a lookup. The server reports a stopped DDEV project with the command that would fix it." A handshake is not a lookup, and a maintainer decides whether the rule reaches it.

The repair that rule points at is a clean refusal. The entry detects the stopped project, writes a JSON-RPC error that names `ddev start`, and starts nothing.

Two costs belong in that decision. The wrapper spends 22 seconds on the first launch, which a client with a short handshake timeout may not wait for. It also runs `ddev start` on every later launch, where the project is already up.

A cold launch that asserts the first byte of stdout is `{` measures either repair.

The Getting Started tutorial depends on this. Its draft tells a reader to close the client, run `ddev start`, and start the client again. That step exists because of this defect, and a repair here removes the reason for it.
