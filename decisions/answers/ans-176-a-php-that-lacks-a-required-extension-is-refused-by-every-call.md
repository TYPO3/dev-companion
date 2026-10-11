---
id: D-ANS-176
title: A PHP that lacks a required extension is refused by every call
date: 2026-10-11
status: open
coveredBy:
  - PhpExtensionsTest
  - StdioServerTest::aPhpWithoutARequiredExtensionIsRefusedWithItsName
---

# D-ANS-176 — A PHP that lacks a required extension is refused by every call

**`Server\PhpExtensions` reads the extensions `composer.json` requires and
checks them once, where the server starts. Where one is missing, the start names
it on stderr, and every tool call answers with that sentence as an error.**

`D-ANS-155` refused the one extension `Fetch` uses, where `Fetch` uses it. That
left a refusal per extension to write, and three to find.

## Evidence

- **CI run 37581763708, 2026-10-07.** The PHP 8.4 job died at the start with the
  `RuntimeException` from `Fetch`. `Upstream::check()` read GitHub there,
  outside any tool call, and nothing caught it. It failed only where no answer
  from the last hour was kept, and passed when somebody ran it again.
- **Read on 2026-10-11.** The code below `src/` calls `mb_strtolower()`,
  `mb_strlen()`, `token_get_all()` and `simplexml_load_string()`.
  `composer.json` required none of the three extensions. The mbstring polyfill
  arrives with the dev packages alone. So an install as a dependency passed
  Composer's check without mbstring, and the first lexical match ended in PHP's
  own error.
- **`php -n` on PHP 8.5.4** lacks curl, dom, mbstring, simplexml and tokenizer.
  Each of them is a package of its own on a distribution, so a PHP can lack any
  one of them.
- **`disable_functions=curl_init` leaves `extension_loaded('curl')` true.** The
  test for `D-ANS-155` took the function away. This check reads the extension.
  So its test starts a PHP that never loaded one, the first required extension
  that PHP loads rather than has compiled in.

## Decided

- The maintainer chose on 2026-10-11 to check at the start, over Composer's
  `platform-check`. That one stops the autoloader in a checkout alone, and the
  model sees only a server that failed. In a project the project's setting
  decides.
- `composer.json` is the list. A class that names the extensions a second time
  goes stale the day somebody requires a new one.
- Every call is refused, `typo3_server_scope` included. A server that lacks one
  extension answers some calls and dies on others. Which ones depends on code
  paths no caller can see.
- The start does not read GitHub while one is missing. `Fetch` and `Upstream`
  carry no refusal of their own.

## Assumed

- That a client shows an `isError` result's text to the model, which is what
  `D-ANS-143` rests on too.

## Wrong if

- A session reports a call that ended in PHP's own error for a missing function.
  Then an extension the code uses is not in `composer.json`.
- A caller who lacks one extension needs the rest of the server, often enough to
  report it. Then the refusal goes per tool.
