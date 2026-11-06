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

`feedback/2026-10-31-073718-a-server-launched-through-ddev-exec-cannot.md` reports this launch entry first, and a reader judges the two together. This report measures the mechanism and corrects the cause. It is not a second report of the same symptom.

That report reads the failure as a container start that takes longer than the client waits. The measurement says otherwise. A longer client timeout repairs nothing, because the stream itself carries DDEV's output.

`install` writes `ddev exec php <entrypoint>` as the launch command wherever the project has a `.ddev/config.yaml`. `Installer::startedBy()` decides that.

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

This repository's own `.mcp.json` carries that entry, so the defect reaches the work here. A session on 2026-11-04 lost two `typo3_feedback_record` calls to about 1800 seconds of silence, and recorded over a direct stdio connection with plain `php` instead. Which form that was is open. The earlier report carries a 1829 second case with the project running, and this session did not establish whether its own project was up.

One trap sits in front of any repair. A first wrapper ran `ddev start` before the server and left stdin attached to it. `ddev start` consumed the client's `initialize` request. The result was zero bytes of stdout, exit 0, and a silent dead handshake, which reads like a server that answers nothing.

## Query

A cold project, with the entry `install` writes. `printf '<initialize request>' | ddev exec php vendor/bin/typo3-dev-companion`, run after `ddev stop`. Then the same with `-q`, and the same warm. Then `printf '<initialize request>' | sh -c 'ddev start -y >/dev/null 2>&1 </dev/null; exec ddev exec php vendor/bin/typo3-dev-companion'`.

## Suggestion

Whatever the repair, stdout carries the protocol and nothing else. That is the part no reading of the rules disputes.

The earlier report bars the repair this session verified. Its words are: do not make the entry start the project.

So the wrapper stands here as evidence rather than as a proposal. `sh -c 'ddev start -y >/dev/null 2>&1 </dev/null; exec ddev exec php <entrypoint>'` answers a cold project in 22 seconds, 6263 bytes, `{` first. It shows that the stream is the fault and that the server is sound.

A sentence in the install output does not reach this, and the earlier report proposes one. It serves the reader who meets the entry a month later. It leaves the entry broken.

The server cannot refuse either. `ddev exec` writes its progress before PHP starts, so nothing this repository runs is in the stream yet.

That leaves the entry itself, and two shapes nobody has measured. One reaches the container without `ddev exec`. The other reads the project's state, writes a JSON-RPC error that names `ddev start`, and starts nothing.

A cold launch that asserts the first byte of stdout is `{` measures any of them.

The Getting Started tutorial depends on the answer. Its draft tells a reader to close the client, run `ddev start`, and start the client again. That step exists because of this defect, and a repair here removes the reason for it.
