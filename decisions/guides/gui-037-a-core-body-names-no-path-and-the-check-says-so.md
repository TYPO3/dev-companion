---
id: D-GUI-037
title: A core body names no path, and the check says so
date: 2026-11-24
status: open
coveredBy:
  - CommitMessageTest::aCoreBodyNamingAPathIsToldSo
---

# D-GUI-037 — A core body names no path, and the check says so

**`typo3_commit_message_guide` warns on a core body that names a path into the
source tree or a fully qualified class name. It counts no sentences.**

The maintainer asked for four body rules on 2026-11-24. An agent had pushed
patch set 2 of core change 97935 with a body of five paragraphs. The rules are
no list, no long path, no sentence the reader does not need, and behaviour
rather than a list of code changes.

## Evidence

- The last thousand merged commits on `main`, read on 2026-11-24. 727 bodies
  have more than two sentences, and 510 more than three.
- A first pattern for a path matched 44 of the same bodies. It took
  `copy/move/paste`, the module id `typo3/backend/modal` and the runtime
  directory `var/transient/`. Each of those describes behaviour.
- The pattern the check uses matches 24. Each one is a source file such as
  `Configuration/Backend/Modules.php` or a fully qualified class name such as
  `TYPO3\CMS\Core\Registry`.

## Decided

- `body-names-a-path`, at `warning`, for `workflow="core"` alone. The
  measurement ran on the core, as for `body-counts-what-it-touched`.
- The pattern takes a file path of three or more segments with a source
  extension, and anything below `typo3/sysext/<extension>/`. It also takes a
  class name of three namespace segments. It takes a URL out first.
- No sentence count. The maintainer chose that with the 727 in front of them.
  The rule of one or two sentences stays in the document and in `D-GUI-029`.
- The two rules a check cannot see stand in `core/contribution/commit-messages`
  alone. That is the sentence nobody needs and the behaviour over the list of
  code changes. The document carries a rejected and an accepted body from change
  97935.
- The four rules go further than the official appendix, and the document says so
  where it states them.

## Assumed

- That a short class name is what a reader needs and a namespace is not. The
  accepted body of 97935 names `isInWebMount()` and `WebMountSiteFinder`.

## Wrong if

- A core reviewer asks for a path in a body that the check reported. Then the
  path carried something the diff did not, and the pattern is too wide.
- A session passes the check with a body of five paragraphs again. Then the
  count the maintainer declined is the check that was missing.
