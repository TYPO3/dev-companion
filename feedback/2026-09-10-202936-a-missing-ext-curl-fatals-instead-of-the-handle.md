---
date: 2026-09-10T20:29:36+02:00
category: bug
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
---

# a missing ext-curl fatals instead of the handle-less path read() already has

## Observation

The task was to make this server's Forge tools answerable from a Claude Code session in this checkout.

Http\Fetch::read() calls curl_init() without establishing that the extension is there, so on a PHP without ext-curl the call ends in an uncaught Error: "Call to undefined function TYPO3\DevCompanion\Http\curl_init()" at src/Http/Fetch.php:156. Over stdio that ends the server process, so the client loses every tool rather than one answer.

read() already has the path this should take. The line under it reads curl_init() returning false as status 0, which the tools render through Unreachable::NOT_ANSWERING. A missing extension throws before that guard can run, so the one case the method cannot get a handle in is the one case it does not answer for.

composer.json requires ext-curl, so an installation honouring the platform requirement never reaches this. Two ways in do: an install that ran with the platform check bypassed, and an entry that starts a PHP other than the one Composer resolved against.

## Query

typo3_forge_lookup with issue 110348, on a host PHP 8.2 without ext-curl

## Suggestion

Guard the handle where read() already guards it, and give the caller the shape it already has for a read it could not make.

The wording wants care rather than reuse. Unreachable::NOT_ANSWERING tells the caller the tracker is reachable in a browser and that nothing here answers offline, which sends somebody to look at the network when the fix is to the PHP the entry starts. A missing extension is local, knowable before any read, and worth its own sentence naming the extension.
