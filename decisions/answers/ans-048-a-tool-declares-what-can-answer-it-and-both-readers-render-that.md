---
id: D-ANS-048
title: A tool declares what can answer it, and both readers render that
date: 2026-08-04
status: open
coveredBy:
  - SourceTest::theAnsweredByCasesAreTheDeclaredSources
  - SourceTest::theDescriptionACallerReadsCarriesThem
  - SourceTest::theOrientationAnswerGroupsEveryOfferedTool
---

# D-ANS-048 — A tool declares what can answer it, and both readers render that

**Every tool declares its sources as `answersFrom()`. The description a client
reads, the `answeredBy` cases and the orientation answer all render from that
one declaration.**

A caller could read what a tool is about and not whether it would answer at all
with the containers down. Three tools declared
`answeredBy: installation | packages` and only ever emitted `installation`, so
the schema promised a fallback that would never arrive.

## Evidence

- Of the 26 offered tools, the installation can answer 8 and the files the
  packages ship 8. The bundled knowledge answers 12, a network service 3 and
  this checkout 2. Several have two of them. Nothing outside the source of each
  tool said so.
- `typo3_configuration_lookup`, `typo3_schema_lookup` and
  `typo3_backend_module_lookup` emit `'installation'` on every path and declared
  both cases. `typo3_project_describe` and `typo3_changelog_lookup` emit
  `'packages'` on every path and declared both.
- Four descriptions restated their own path in prose, in four different
  wordings. One of them named `answeredBy` from inside the sentence that the
  field was about to contradict.

## Decided

- The declaration is a `Source` enum on the tool, not a string list. A case that
  does not exist fails to compile rather than fails a test.
- `Registry::definitions()` appends the clause to the description. So a tool
  that gains or loses a source cannot keep the old statement where a client
  reads it. Every description ends the same way, which is what makes it
  skimmable at the cost of one short sentence per tool per session.
- `Schema::answeredBy()` takes the tool's own declaration and keeps the two
  sources that label a single answer. A knowledge file and a network service are
  what a whole tool reads, never one call, so they are not cases of it.
- `typo3_server_scope` groups the offered tools by source rather than lists a
  source per tool. The question a caller asks it is about the state of the
  machine, and a tool with two sources belongs under both.
- `tools:index` writes `documentation/server/tools/answer-sources.rst` from the
  enum, and every tool page links its sources into it. The names alone do not
  carry the difference that matters, and a reader who meets `packages` for the
  first time has one place to go.

## Assumed

- A source list is stable enough to be worth a place at the end of every
  description. A tool that changed sources per call would make the clause a lie
  the schema cannot correct.

## Wrong if

- A new tool arrives whose answer comes from something none of the five names.
  The writer picks the nearest case to leave the enum alone. The clause then
  says something true of no source.
- The clause meets a client's tool-list budget and loses. 26 tools carry it in
  every session, and nothing here has weighed that against what a client will
  hold.
