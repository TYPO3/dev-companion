---
id: D-KNW-069
title: 'A browser in a container reaches a site on the router'
date: 2026-08-10
status: open
coveredBy:
  - KnowledgeTest::theTestRunGuideNamesTheBrowserCheckDocumentWithTheE2eSuites
---

# D-KNW-069 — A browser in a container reaches a site on the router

**Joining `ddev_default` is the whole of it, because the router carries each
project's hostname as a network alias.**

The other half of
[`D-KNW-068`](knw-068-looking-at-a-backend-change-is-a-suite-the-core-already-carries.md).
The prepared instance is a styleguide, and the defect that needs the developer's
own content still had no place to show.

## Evidence

Measured against the DDEV project that runs on this machine, which is the same
installation the report came from.

- `docker port ddev-router` publishes 80 and 443 on `127.0.0.1` alone. A
  container started with `--add-host host.docker.internal:host-gateway`
  therefore reaches the host's gateway address and nothing answers. curl exits
  7, which is the connection refused the session reported.
- `--network ddev_default` alone answers 200 over HTTP for the project's own
  hostname, with no `--add-host` at all. The router's aliases on that network
  are its container name and every project hostname it serves, which is what
  resolves the name inside the container. The session's route, a read of the
  router's address in that network and a map of the hostname onto it, also
  answers 200. It is what a hostname the resolver cannot answer still needs.
- HTTPS from a plain container fails verification. The certificate authority
  sits on the host and not in the image. `ddev describe -j` carries `httpurl`
  beside `primary_url`, which is the way around it that changes no
  configuration.
- This run checked Node's resolution directly.
  `require.resolve('@playwright/test')` from a directory outside the one that
  holds `node_modules` throws, and resolves once a `node_modules` symlink sits
  beside it.
- The core checkout ignores `/typo3temp/*` and none of `Build/typo3temp/`. So an
  output path relative to the directory the run started in lands in a committed
  directory.

## Decided

- It is a document rather than hints. The gap was a procedure with an order to
  it, which is what
  [`D-FBK-043`](../feedback/fbk-043-a-structure-is-answered-with-a-document-rather-than-with-a-rule.md)
  answers with a document. `any/testing/browser-check`, because a session
  reaches a DDEV site the same way whoever works on it.
- It carries the simple route first and the feedback's own as the case that
  needs it. A wildcard additional hostname is not an alias Docker can answer,
  and that is the one place a session has to read the router's address.
- `browser-tests` gains the words a reviewer uses: visual check, screenshot,
  render correctly, reproduce in the backend. The session had that hint listed
  under `omittedHints` and skipped it. Its own vocabulary was the vocabulary of
  somebody who writes a spec.
- The document names no `-s` suite. Which suites exist is per branch and lives
  in `test-suite-hints.json`, which `KnowledgeTest` holds prose to.

- The page ends where the request reaches the site, and says so since
  2026-09-19. A session ran it end to end on first use, the wildcard host and
  the harness placement included, and found the one thing it had to look up
  elsewhere was the backend login of a worktree that inherited its database.
  That is the add-on's business, `feedback/2026-09-18-093214`, `D-FBK-018`.

## Assumed

- That the router's aliases are DDEV's behaviour rather than this machine's
  configuration. This run read it from the router that runs and it matches what
  the network is for. Nothing here tried a second DDEV version.
- That the `Build` directory is what a container run inherits. It is where the
  core's own npm scripts run and where the session saw its screenshots land.
  This run started no browser container of its own.

## Wrong if

- A session on another DDEV version finds the hostname does not resolve on
  `ddev_default`. Then the alias is not the mechanism and the explicit mapping
  is the route, not the fallback.
- Somebody reaches for this document to write a committed browser suite. That is
  `project/testing/playwright`, and a `whenToUse` that lets a reader confuse the
  two is the thing to fix.

## Since then

The session six hours after this document landed held the page's id and never
opened it. It told its reader five times that it could not judge the change
visually. Re-run from its own paths, the suite guide names no page beside the
two suites. The hint lookup selects a domain where this one is neither returned
nor listed. The answer names the page where the query already says a browser is
in play. So it delivers to a caller who has decided that, and the reviewer of a
visual patch is the caller who has not. That is the second step of the ladder
rather than the first.

A session measured two other placements and took neither, and the answer the
session already read is the cheaper place. **Built on 2026-08-11**: the suite
guide names the page under its blocks wherever one is an e2e suite.
