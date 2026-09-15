---
id: D-ANS-155
title: A read on a PHP without curl names the extension
date: 2026-09-15
status: open
coveredBy:
  - StdioServerTest::aPhpWithoutCurlIsRefusedWithTheExtensionNamed
---

# D-ANS-155 — A read on a PHP without curl names the extension

**`Fetch::read()` refuses a read on a PHP that has no `curl_init()`, and the
refusal names ext-curl, the PHP and what to do. It does not answer as a source
that did not answer, and it does not leave PHP's own error to say it.**

`composer.json` requires ext-curl, and two ways in pass that: an install with
the platform check off, and an entry that starts a PHP other than the one
Composer resolved against.

## Evidence

- **The report.**
  [`feedback/2026-09-10-202936`](../../feedback/archive/2026-09-10-202936-a-missing-ext-curl-fatals-instead-of-the-handle.md),
  this checkout, `claude-opus-5[1m]`. `typo3_forge_lookup` on a PHP 8.2 without
  ext-curl ended in `Call to undefined function curl_init()` at the line that
  makes the handle. The report says the server process went with it.
- **Run again on 2026-09-15** over stdio, with `php -n` for a PHP without the
  extension. The call answered `isError: true` with that PHP error as its text.
  The request after it was answered. So the process stands since the catch
  `D-ANS-143` put in on 2026-09-04, and the report saw an earlier checkout or
  inferred the exit.
- **What is left is the text.** The error names a function in this server's
  namespace and no extension. The handle-less path under it maps onto
  `Unreachable::NOT_ANSWERING`, whose sentence sends the reader to the network
  and to a browser. Both are wrong about a fault that is local and known before
  any read.
- **Every source reads through `Fetch`**, so the fault and its sentence are the
  same for the tracker, the review server, the registry and the manuals. A cause
  of its own would travel through seven return shapes to say one sentence.

## Decided

- **A `RuntimeException` where the handle would be made**, and only where no
  transport stands in. `Sdk\ToolHandler` carries its message to the caller as
  `D-ANS-143` decided for every refusal, so the shape is the one a refusal has.
- **The message names ext-curl, `PHP_BINARY` and `PHP_VERSION`.** The path is
  what tells an install with the check off from an entry that starts another
  PHP. `Typo3Cli` already names the interpreter it runs the console with, so the
  answer names nothing new.
- **The check is `function_exists('curl_init')`**, so `disable_functions` in a
  test takes the function away from a PHP that has it. The case runs on every
  PHP the suite runs on, rather than only where an ini leaves curl out.
- **The `false` from `curl_init()` stays a status 0.** That is a handle the
  extension could not make, which is the source's own kind of miss.

## Assumed

- That a client shows an `isError` result's text to the model, which is what
  `D-ANS-143` rests on too.

## Wrong if

- A session on a PHP without curl reports the tracker as down, or spends calls
  on the network. Then the text did not reach it, and the cause has to travel as
  data after all.
- A refusal that names `PHP_BINARY` turns out to carry a path the caller may not
  see. Then the message names the version alone.
