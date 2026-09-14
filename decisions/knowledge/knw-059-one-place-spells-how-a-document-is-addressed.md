---
id: D-KNW-059
title: One place spells how a document is addressed
date: 2026-08-04
status: open
coveredBy:
  - StructureTest::onlyTheCorpusSpellsHowADocumentIsAddressed
---

# D-KNW-059 — One place spells how a document is addressed

**`Documents` owns the resource prefix and the conversion both ways, and no
other class in `src/` writes a document URI of its own.**

The prefix moved from `typo3://core/` to `typo3://guides/` in one commit, and
the commit had to find it in six places to do it. Three of them were string
literals in classes that had no business with it.

## Evidence

- `Result\Prose` built the URI three times from a literal, `Tool\HintLookup`
  twice, and `Sdk\ResourceHandler` held the constant the other two did not use.
- The two that did not use it may not. `Result\` and `Tool\` are above the SDK
  adapter, and a reach into `Sdk\` for a name would invert that.
- The id is already the one way a caller reaches a document:
  `Documents::search()`, `read()`, `description()`, `isCoreOnly()`, `forHint()`,
  `TaskIntents`'s rule list, `ScriptLookup`'s one document. That half was
  uniform and stays as it is.
- What is not a problem of one address is the resource **name**. The SDK holds
  it to alphanumerics, underscores and hyphens, so `Server\Factory` flattens the
  id there. That is a protocol constraint rather than a second identifier,
  `D-KNW-058`.

## Decided

- `Documents::URI_PREFIX`, `Documents::uri()` and `Documents::idOf()`. The
  corpus owns its own address, because the tools and the answer shapes need it
  and neither may ask the adapter.
- ResourceHandler::DOCUMENT_PREFIX is gone rather than kept as an alias. Two
  names for one constant is the thing this entry is about.
- The id stays the argument every read call takes. A URI passed into `search()`
  would need a strip again at the other end, which is the same conversion moved
  rather than removed.
- The guard runs over `src/` alone. A test that drives the wire spells the URI
  out on purpose. An expectation computed from the code under test asserts that
  the code equals itself.

## Assumed

- Prose is not covered by any of this. A URI written into a document, a skill or
  a tool description is text a reader follows, and `bin/cli links:check` does
  not check it either.

## Wrong if

- A URI written in prose survives a move of the namespace and resolves to
  nothing.
- The index URI and the document prefix drift apart, since one lives in
  `ResourceHandler` and the other in `Documents`.
