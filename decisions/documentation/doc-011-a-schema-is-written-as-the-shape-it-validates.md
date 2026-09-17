---
id: D-DOC-011
title: A schema is written as the shape it validates
date: 2026-08-03
status: open
coveredBy:
  - ToolSurfaceTest::everyPageIsWhatTheServerDeclares
---

# D-DOC-011 — A schema is written as the shape it validates

**The tool surface renders a schema as YAML and not as a bullet list. A key per
field, nested where the schema nests, the type as the value.**

A bullet list of fields could not say where a field sits, which is the one thing
a caller reads a schema for.

## Evidence

- Every field came out at the same level however deep it sat. In
  `typo3_server_scope`, `covers` and the `topic` of one of its entries were two
  bullets in one list. So the answer read as though it carried a top-level
  `topic`, and eleven of that tool's twelve nested fields read the same way.
- The renderer computed an indent and passed it to `Wrap`, which trims what it
  gets. The nest went in and out on the same line, which is why nothing looked
  broken in the code.
- The list cost more characters than the shape does. `- ` plus a code span plus
  an italic type plus `— ` per field, against `name: type`.

## Decided

- One key per field, the fields of an object or of a list entry nested under it,
  and the value is the type. `[string]` is a list of strings; a `- ` opens a
  list of objects.
- The description is a comment above the key rather than behind it. Behind it, a
  sentence pushes the shape off the right of the block and the reader loses the
  column the keys line up in.
- A field carries `# optional` where it may be absent, and nothing where the
  schema requires it. Required is the promise a client validates against, so it
  is the default and the exception is what carries the mark.
- A closed set stands in the comment as `One of: a, b, c.`, before the
  description. It is what a caller has to pass rather than something to
  understand.
- The block has a `yaml` fence and is not valid YAML in the sense that it parses
  to the answer. It is the shape of one, and a reader who pastes it gets the
  field names and the nest, which is what they came for.

## Assumed

- A reader takes `name: string` as a type rather than as a value. No measure
  covers that; the alternative is a second column of `(string, required)`
  annotations, which is the list this replaced.

## Wrong if

- Somebody validates against a page instead of against the `outputSchema` the
  tool declares, and files a defect because the page does not parse. Then the
  block needs a language nothing renders as data. But the schema itself is what
  a client should read, and the page says so.
