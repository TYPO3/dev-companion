---
id: D-KNW-058
title: The document namespace is scope first and derived from the file
date: 2026-08-04
status: open
coveredBy:
  - KnowledgeTest::everyDocumentIsScopeThenTopicThenName
---

# D-KNW-058 — The document namespace is scope first and derived from the file

**A document is `<scope>/<topic>/<name>` below `knowledge/documents/`, that path
is its id, and its resource URI is the path under `typo3://guides/`.**

`typo3://core/` was true of every document while every document was the core's
own process. The server announces the first one that answers for a package alone
to every client under a prefix that says the opposite.

## Evidence

- `typo3://core/typo3-extension-phpunit-setup` is what a client gets today. The
  page is about the setup of a package, and the core's own harness is a
  different one. The prefix states the scope and states it wrongly.
- The scope was already declared twice before that. `Documents::isCoreOnly()`
  reads it from the `covers` row, and the file name carries it as a word
  somebody chose to include.
- Nothing in the resource layer resists a path. `ResourceHandler` takes
  everything after `typo3://core/` as the id, so a segmented one already
  resolves. What stops it is `depth(0)` in `Documents::documents()` and the
  `[a-z0-9-]+` in `ScopeTest`.
- That `depth(0)` is a guard rather than an oversight. `Paths::documents()`
  records why. A place in the directory is what publishes a file, and a readme
  beside the corpus became `typo3://core/readme` with nobody's decision.
- The rename reaches prose rather than code. The URIs stand in the corpus, in
  skills and in `server-scope.json`, and `bin/cli links:check` and
  `ToolNamingTest` are what find the ones a sweep misses.
- A resource **name** may not carry the path. `Mcp\Schema\ResourceDefinition`
  holds one to alphanumerics, underscores and hyphens and rejects the definition
  outright, so the server does not start. Only the URI is free-form, which is
  what the protocol addresses a resource by.
- The move found a document with the wrong scope. `commit-messages` declared
  that it holds everywhere and is the core's process throughout: `Resolves:`,
  `Change-Id:`, the changelog files, the Security Team. So the move corrected
  the label rather than carried it into a directory.

## Decided

- The first segment is the scope, spelled as `Knowledge\Scope` spells it, and it
  is the scope. A move of a document between those directories is how a session
  rescopes it, so no field repeats it and the two cannot drift apart.
- Three segments, every time: the scope, one topic, one name. A shape that holds
  for the extension pages and not for the core's is two conventions. The core's
  are the four a reader meets first.
- The topic segment is free text rather than a closed list. Six documents are
  too few to know what the list would be. A vocabulary guessed before its
  members exist is the abstraction `AGENTS.md` calls speculation until the
  second caller.
- One topic under two scopes is the ordinary case rather than a collision.
  `core/testing` and `extension/testing` are the row and the column `D-KNW-008`
  describes, and the namespace is where the crossing becomes visible instead of
  being carried by prose.
- The prefix is `typo3://guides/`. `core` cannot stay: it names one of the
  scopes below it now, and a prefix that repeats a segment says nothing.
- A rule replaces `depth(0)` rather than drops it. A file publishes itself only
  from a directory named for a scope. So a readme anywhere else in the tree is
  not a resource, which is the property the depth limit stood in for.
- The five documents there are move and their URIs change with them. A URI a
  client holds in a note goes stale. The alternative is a prefix that lies about
  every document that is not the core's.
- The resource name is the id with its separators flattened, because the
  protocol will not take the path there. The URI keeps its segments, and that is
  the identifier anything outside this checkout addresses a document by.
- Withholding a core document outside the core now leaves a hole where that
  document used to transfer, and the hole is filled by naming the tool that owns
  the subject rather than by a shortened copy of the page. `D-DOC-013` already
  settled that the commit conventions are one measure and it is
  `typo3_commit_message_guide`.

## Assumed

- Clients read the resource list per session, so a changed URI costs a stale
  note rather than a broken integration. Nothing here measures what a client
  keeps.
- The scopes make a usable first segment. `any` reads oddly as a directory and
  is the honest name for a document that holds everywhere.

## Wrong if

- A document lands in a directory that is not a scope, so nothing publishes it
  and nothing says somebody meant it for the list.
- A document sits at another depth than three, so a caller who learned to read
  one URI cannot predict the next.
- The topic segments drift into synonyms of each other, which is what a closed
  list would have prevented and free text cannot.
- The server publishes a file that is not a document because it sits inside a
  scope directory.
- The scope in the path and the scope a `covers` row states for the same
  document disagree.
- A URI written in prose survives the move and resolves to nothing.
