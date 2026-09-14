---
id: R-ANS-022
title: 'A resource is picked out of a list'
status: held
restsOn: [D-AUD-004]
heldBy:
  - ResourceSurfaceTest
  - ResourceSurfaceTest::everyLinkASkillWritesResolvesToAResourceThisServerServes
  - ResourceSurfaceTest::onlyAPublishedSkillIsOffered
  - ScopeTest::everyKnowledgeDocumentIsAnnouncedByTheScope
  - ScopeTest::everyPublishedSkillIsAnnouncedByTheScope
  - StdioServerTest::aTaskWorkflowIsServedWithWhatItSendsItsReaderTo
  - StdioServerTest::theResourceListCarriesWhatAPickerChoosesBy
---

# R-ANS-022 — A resource is picked out of a list

**A caller picks a `typo3://` resource out of a list. So it says what it is and
what it obliges, and every audience has one to pick.**

Resources are application-driven. The host offers them for selection, or the
user searches them, where the model calls a tool in the middle of a task. The
list is therefore the whole of what the choice rests on, and the spec says what
a client reads it by. `description` says what the offer is,
`annotations.priority` sorts what is worth the context, and `size` says what a
read costs. A resource offered as a name and a mime type is one nobody picks.

Who does the pick is the second half. The tool list serves the three audiences
of
[`R-AUD-001`](../audience/aud-001-core-extension-and-site-work-are-each-served.md).
The document corpus behind `typo3://guides/{id}` serves one, because most of it
is the core repository's own and says so. The published task skills are the
other way round. Most of them are extension, sitepackage and project work, and
two are the core's, which `knowledge/server-scope.json` says. The server offers
both families, so each audience picks something that holds where it works, out
of prose somebody writes and maintains either way.

What the caller picks has to be the whole of what it picked. A skill is a
directory. Its body is short routing, and every one of them opens with a route
to `references/base.md`. That is a file in no skill here; `Installer` writes it
at publication. So the server serves the body at `typo3://skill/{id}/SKILL.md`.
There the relative links it already carries resolve to the reference URIs this
server answers, and nothing needs a rewrite to point somewhere. The references
are a resource template rather than list entries. A reader follows them from the
workflow that names them, and a checklist offered beside its own workflow is an
entry nobody can choose between.

## From

The read of 2026-08-04 that found no file in `requirements/` with `typo3://` in
it at all. And the answer that settled what the surface is for. It serves all
three audiences rather than stays the core-contribution corpus it was. The card
priced the three ways to close that, and the skills won because they cost
`knowledge/documents/` nothing. That directory is also what `typo3_rule_lookup`
searches, and `D-ANS-040` measured a query that fell from 0.508 to 0.462
coverage because four sections went in elsewhere.

## Held by

- `ResourceSurfaceTest`, for what a client reads before it picks. Every resource
  describes itself, says who its answers oblige, and declares the size and the
  priority a picker sorts by.
- `ResourceSurfaceTest::everyLinkASkillWritesResolvesToAResourceThisServerServes`,
  for the half a description cannot carry. Every link a workflow writes,
  resolved against the URI it lives at, is a URI this server answers.
- `ResourceSurfaceTest::onlyAPublishedSkillIsOffered`, so a draft does not slip
  into publication by the back door.
- `ScopeTest::everyKnowledgeDocumentIsAnnouncedByTheScope` and
  `ScopeTest::everyPublishedSkillIsAnnouncedByTheScope`, for the coverage that
  says which audience each one is for. Nothing else says it, and what no topic
  names goes out as core-only.
- `StdioServerTest::theResourceListCarriesWhatAPickerChoosesBy` and
  `StdioServerTest::aTaskWorkflowIsServedWithWhatItSendsItsReaderTo`, on the
  list and both families as they go over the wire.
