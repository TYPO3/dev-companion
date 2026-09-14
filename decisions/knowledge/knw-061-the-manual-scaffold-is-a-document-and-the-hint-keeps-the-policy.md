---
id: D-KNW-061
title: The manual scaffold is a document and the hint keeps the policy
date: 2026-08-04
status: open
coveredBy:
  - ScopeTest::everyKnowledgeDocumentIsAnnouncedByTheScope
---

# D-KNW-061 — The manual scaffold is a document and the hint keeps the policy

**A document in `knowledge/documents/` says what an extension's `Documentation/`
directory consists of and which command renders it. `extension-documentation`
goes on with the policy.**

A session told an extension needs a manual read four installed packages to find
out what to put in the directory.

## Evidence

- `feedback/2026-08-04-175804`. The hint says a manual lives in `Documentation/`
  with `Index.rst` as entry point and `guides.xml` as renderer configuration,
  and that it ships with the package. The official page recommends
  `Documentation/` over a single README and says why. Neither says what has to
  be in the directory.
- What the session read instead:
  `vendor/typo3fluid/fluid/Documentation/guides.xml` and
  `vendor/typo3/cms-dashboard/Documentation/guides.xml` for the file shape, plus
  `Includes.rst.txt` and the `Index.rst` header block with its toctree. It found
  the renderer and the flag that makes a warning fail a build without help from
  here.
- The same batch carries the other half of this domain:
  `feedback/2026-08-04-175935` is an audit that never asked the convention at
  all — `D-SKL-019`. Two sessions of one day arrived at the manual from
  different directions, and neither got a scaffold out of this server.
- [`D-FBK-043`](../feedback/fbk-043-a-structure-is-answered-with-a-document-rather-than-with-a-rule.md)
  is the shape. A session that found a *structure* unclear gets a document,
  because a hint states one thing and a file inventory is not one thing.
  `knowledge/documents/extension/testing/phpunit.md` is the neighbour it is
  written beside.

## Decided

- The judgement is **step 1a**, the knowledge is absent, and this entry **takes
  it on**. That is a document below
  `knowledge/documents/extension/documentation/` that declares what it is and
  when to reach for it as
  [`D-KNW-057`](knw-057-a-document-declares-what-it-is-and-when-to-reach-for-it.md)
  requires.
- The hint is not grown into the scaffold. It keeps the policy: where a manual
  lives, that it ships with the package, what a break owes its readers. The
  document names it in its own `hints:` front matter, which is how the two are
  already tied.
- Not closed on the spot. What a `guides.xml` has to contain is a fact about the
  renderer. The writer reads it from the renderer's own documentation and from
  packages that ship one, never copies it out of the report.

## Assumed

- That the scaffold is stable enough to write down. Two packages agreed on it in
  the session's account, and the todo's first step is whether a third and the
  renderer's own documentation do.

## Wrong if

- The file shape turns out to move with the docs theme or the TYPO3 major, and
  the document carries no bound for it. Then this is a statement with a `since`
  rather than a page.
- A session that read the document still opens a vendor package to copy a file.
  Then the gap was a template the server hands over, not a description of one.

## Since then

A session established the scaffold and wrote it. The minimal set is two files
and the official reference says so. What the renderer needs of the configuration
is three attributes, and the rest produces the edit link and the header.

A render verified both rather than a read, and the measurement that made the
page worth the write is the exit code. A tree whose entry point includes an
absent file logs the failure, prints success and exits 0. The same run with the
strict flag exits 1. So the flag is what turns the renderer into a check, which
neither the official page nor the README states.

The page names two files that look like the template and are not it. A session
that copies from a real published extension can land on the replaced renderer.
