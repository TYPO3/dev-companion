---
id: D-ANS-133
title: A describe field is a file's own declaration
date: 2026-09-02
status: open
coveredBy: []
---

# D-ANS-133 — A describe field is a file's own declaration

**`typo3_project_describe` and `typo3_extension_describe` report what a file
declares; a field that would take an inference or a TypoScript parser is
declined, and the knowledge goes into a hint instead.**

Both fields asked for on 2026-08-31 would have read as the file's own word for
something no file says.

## Evidence

- [`feedback/2026-08-31-234745`](../../feedback/archive/2026-08-31-234745-told-four-times-that-code-is-english-i.md)
  asks `typo3_project_describe` to state the project's code language. Its
  session was told five times over eighty minutes that the code is English, ran
  four bulk translation passes, and wrote German strings into a new test file
  fifteen minutes after the last of them.
- What the repository declares is the source language of its XLF files, and
  `typo3_extension_describe` already reports it per file. The field's own
  description draws this boundary: "This is what the file says, not what it
  should say."
- Nothing else there carries it. An `.editorconfig` has no such field, and a
  contributing file states it in prose where it states it at all.
- [`feedback/2026-08-31-234039`](../../feedback/archive/2026-08-31-234039-extension-describe-on-fluid-styled-content.md)
  asks `typo3_extension_describe` to report the top-level TypoScript objects a
  setup defines. Its session read `contentElements: []` on
  `fluid_styled_content`, drew no conclusion, and took the dependency without
  noticing.
- What that reading takes, measured in `.checkouts/main` on 2026-09-02: the
  set's `setup.typoscript` is one `@import` of
  `EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript`, which
  imports `./Helper/ContentElement.typoscript`, where `lib.contentElement` opens
  with a `>` clear and a `FLUIDTEMPLATE`. Two imports, one `EXT:` and one
  relative, before the first assignment.
- The container does not answer it either. The assembled setup is built per site
  and per request rather than held in the container, so `Typo3Runtime` has
  nothing to ask for.
- Both reports landed everywhere but the tool. The practice is a hint,
  `verifying-a-change-against-the-installation`, whose `appliesTo` carries
  "which language the code is in"; what `lib.contentElement` provides is
  `sitepackage-fluid-styled-content` of 2026-09-01.
- The corpus: one session, one directory, one night. `bin/cli feedback:list` on
  2026-09-02 holds nothing else of either shape, and the code-language report
  says of itself that most of the failure is the model's.

## Decided

- *Proposed* under `documentation/records/judging.rst`, put to the maintainer on
  2026-09-02 and answered there: neither field is built.
- What a describe answer carries is a declaration somebody wrote in a file it
  opens. That is what makes every other field of it checkable against the
  checkout the caller is standing in.
- Against the inference. A project with English labels and German comments
  declares exactly the same thing, so the sentence would be a claim about the
  repository that the repository does not make.
- Against the half-parser. A reader that reports the objects it found through
  the imports it could resolve is a wrong answer wearing a complete one's
  clothes, and `contentElements: []` already showed what a caller does with a
  list they read as exhaustive.
- The caller's question is answered by the hint in both cases, which is where a
  statement about TYPO3 belongs anyway.
- Nothing holds this. A test asserts the demand rather than its absence, and
  there is no field to assert against.

## Assumed

- That the XLF source language is the only code-language declaration an ordinary
  project makes. No corpus of projects was swept for another.
- That a caller who reads `contentElements: []` reaches the hint. Its
  `appliesTo` carries "do I need fluid_styled_content" and "lib.contentElement",
  and nothing measures whether the describe answer's caller asks for it.

## Wrong if

- A second session reports the same convention failure and names the project
  answer as where it would have read the language. Then one report was the
  evidence and the sentence was worth its inference.
- A session concludes from `contentElements: []` that an extension is inert for
  it while that extension's setup is exactly what its project consumes. Then the
  file list is the miss, and the hint did not reach the moment.
- A project turns up declaring its code language in a file this server already
  opens. Then it is a reading rather than an inference, and the ground for this
  decline is gone.
