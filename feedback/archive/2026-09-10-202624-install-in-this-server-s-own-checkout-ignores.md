---
date: 2026-09-10T20:26:24+02:00
category: bug
status: closed
closed: 2026-09-15
model: claude-opus-5[1m]
---

# install in this server's own checkout ignores the DDEV project that checkout is

## Observation

The task was to make this server's Forge tools answerable from a Claude Code session in this checkout.

install --agent=claude wrote an entry naming the host PHP and the absolute entrypoint path, in a checkout that has a started DDEV project of its own. The host PHP 8.2 has no ext-curl, which composer.json requires. So every network-backed tool fails, and it fails as a fatal error rather than as the tool's own answer: typo3_forge_lookup died with "Call to undefined function curl_init()" in src/Http/Fetch.php, which through the transport takes the server process down instead of returning the unreachable text ForgeLookup declares for exactly this case.

Installer::startedBy() reaches its DDEV branch only when installedEntrypoint() finds the server below the project's bin directory or vendor/bin. In this checkout the server is the Composer root package, so its own binary is never linked into vendor/bin, installedEntrypoint() answers null, and the branch is never tried. The docblock reads that null as the server "being run from a checkout elsewhere, which the container cannot see either". That holds where another project depends on this one, and not where the checkout is itself the DDEV project: the container sees it at /var/www/html.

Rewriting the entry by hand to ddev exec php bin/typo3-dev-companion answers. The container PHP is 8.4.22 with curl, DDEV's own warnings go to stderr rather than stdout, and initialize, notifications/initialized and tools/call returned issue 110348 whole over JSON-RPC.

## Query

php bin/typo3-dev-companion install --agent=claude, run in this server's own checkout, with .ddev/config.yaml beside it and the DDEV project started

## Suggestion

Turn the DDEV branch on where .ddev/config.yaml sits beside the entrypoint being installed, rather than on installedEntrypoint() having found a path. Whether this server is a dependency of the project and whether the container can see the entrypoint are two questions, and startedBy() currently answers the second with the first. Where the project is this server's own checkout, the entry a client can start is ddev exec php with the entrypoint relative to the project root.
