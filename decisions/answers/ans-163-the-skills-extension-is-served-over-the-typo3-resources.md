---
id: D-ANS-163
title: The Skills extension is served over the typo3:// resources
date: 2026-09-18
status: open
coveredBy:
  - ResourceSurfaceTest::theManifestOfASkillIsComputedFromTheBytesAReadReturns
  - StdioServerTest::theSkillsExtensionListsEachWorkflowWithTheManifestAHostVerifiesAReadBy
---

# D-ANS-163 — The Skills extension is served over the typo3:// resources

**The server declares `io.modelcontextprotocol/skills` and answers `skills/list`
and `skills/get` with a manifest over the `typo3://skill/` resources it already
serves.**

A host may not tell a skill from a page by its URI, and it issues the two
methods only after it has seen the declaration. Without it every published
workflow was a markdown resource to every host, whatever its shape.

## Evidence

- **Read on 2026-09-18**: the overview at
  modelcontextprotocol.io/extensions/skills/overview and the stable
  specification in the ext-skills repository. A server that declares the
  extension implements `skills/list` and `skills/get` and serves the files
  through `resources/read`. A list entry carries the URI of `SKILL.md`, every
  front matter field unchanged, and a manifest with URI, SHA-256 and byte size
  of each file. Both results carry `resultType`, `ttlMs` and `cacheScope`. A URI
  that names no skill is `-32602`. The segment above `SKILL.md` equals `name`,
  and relative links resolve against it. `skill://` is a SHOULD and another
  scheme a MAY. A host must not identify a skill by the scheme alone.
- **What held already.** A skill is a directory with `SKILL.md` and
  `references/`. Every `name` equals its directory. The body is served at a URI
  that ends in `/SKILL.md`, so its links resolve onto the reference template,
  `R-ANS-022`. The `resources` capability was declared.
- **What did not.** No declaration, no `skills/list`, no `skills/get`. A
  reference name nothing serves answered `-32603` Internal error, because
  `Skills::reference()` threw a `RuntimeException` the SDK reads as a failure of
  its own.
- **mcp/sdk v0.8.1**, the newest release on 2026-08-29.
  `Builder::enableExtension()` takes an `ExtensionInterface` with its messages
  and handlers, and ships one extension, `McpApps`. Its 2026 codec stamps
  `ttlMs` and `cacheScope` on its own methods alone, so a method an extension
  adds carries them itself. The stdio transport negotiates up to `2025-11-25`,
  and the declaration goes out in the `initialize` capabilities on every
  revision.

## Decided

- **The scheme stays `typo3://`.** Every prose answer, two tool descriptions and
  every published skill body name it, and `D-SCO-010` holds it against the draft
  contract. The declaration is what makes the resources skills, so the scheme
  buys nothing.
- **The manifest is computed from the bytes a read returns**, in
  `Skills::manifest()`. `references/base.md` is a file in no skill directory
  here, `D-SKL-001`, so a hash of the directory would list a file the read
  serves from elsewhere.
- **`SkillsResult` carries `resultType`, `ttlMs` and `cacheScope` on every
  revision.** The scope is public, since what is listed is the same file for
  every client. The ttl is the specification's own example.
- **`directoryRead` stays at its default.** The manifest is complete, so a host
  has nothing to ask a directory for.
- **The `typo3://guides` index keeps its skill listing.** The model reads the
  index through a resource, and the host reads `skills/list` through the
  extension. Two readers, so two lists, and removing the index half would leave
  the model without the reference URIs.
- **Front matter is parsed once**, in `Skills::frontMatter()`, and the regex
  field reader went. The extension hands every field on unchanged, and a second
  parser for the same block could disagree with the first.
- **A URI that names nothing answers with the code the revision fixes.** The
  handlers throw `ResourceNotFoundException`, which the SDK renders as `-32002`
  before `2026-07-28` and `-32602` from it. `skills/get` answers `-32602` on
  every revision, as the specification binds it.
- Against a second listing page in the manual. The resource page says what the
  extension adds, and `StdioServerTest` reads the manifest off the wire.

## Assumed

- That a host which ran `bin/typo3-dev-companion install` and also speaks the
  extension holds the installed copy and the served one apart. The specification
  binds a served skill's identity to the server and the URI, and the copy on
  disk has a path. No session has watched a host that does both.
- That no host asks for `resources/directory/read` where a manifest is complete.

## Wrong if

- A host lists the skills and declines them over the scheme. Then the SHOULD was
  a MUST in practice, and the scheme moves at the cost
  `documentation/server/interface-contract.rst` states.
- A host reports a digest mismatch on a file this server serves unchanged. Then
  something between `Skills::reference()` and the wire rewrites the bytes, and
  the manifest has to hash what that step emits.
- A host loads one workflow twice, once from the install and once from the
  extension, and the two disagree after a release. Then the install and the
  resource need one version marker a host can compare.
