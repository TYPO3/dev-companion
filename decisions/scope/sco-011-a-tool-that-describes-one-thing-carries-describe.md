---
id: D-SCO-011
title: 'A tool that describes one thing carries `describe`'
date: 2026-08-04
status: open
coveredBy:
  - ToolNamingTest::everyToolIsNamedSubjectThenVerb
  - ToolNamingTest::everyToolNameAnAnswerNamesIsRegistered
  - ToolNamingTest::everyToolNameWrittenInTheKnowledgeBaseIsRegistered
  - ToolSurfaceTest::theIndexReachesEveryToolAndTheDirectoryHoldsNoOther
---

# D-SCO-011 — A tool that describes one thing carries `describe`

**typo3_project_scope and typo3_extension_scope become `typo3_project_describe`
and `typo3_extension_describe`. `describe` joins the tool verbs as the sixth,
and `Knowledge\Scope` stays as it is.**

`scope` named two things that both go out on the wire. One is the enum whose
cases are `core`, `project`, `extension`, `any` and `uncertain`. The other is
the tool verb for what a source covers. The two tools whose names read as cases
of that enum were also the two with the wrong verb, so one rename settles both.

## Evidence

- The four descriptions say which verb each tool actually has.
  `typo3_server_scope` opens "Orientation for this server: what it covers and at
  which depth, what it deliberately does not cover". `typo3_snapshot_scope`
  opens "Report whether component contracts come from the active installation or
  the bundled fallback … what they cover". Both state the coverage of a source.
- typo3_project_scope opens "Describe the project around the TYPO3 installation
  this server was started in". typo3_extension_scope opens "Describe what one
  installed extension registers". Both describe one thing the caller named, and
  neither states a boundary.
- The two tools whose verb was wrong are exactly the two whose subject is a case
  of `Knowledge\Scope`: `project` and `extension`. A client gets
  typo3_project_scope and, inside the answers, a `scope` field that can read
  `project`. Nothing said the two have nothing to do with each other.
- `Knowledge\Scope` is the one vocabulary `D-KNW-005` collapsed four spellings
  into: `binding`, `provenance`, `audience` and an `outsideCore` boolean. Every
  statement in `knowledge/` declares it, `Scope` computes it for every path, and
  the answers read it back in `scope` and `scopes` fields.

## Decided

- Two tools move, not four. `typo3_server_scope` and `typo3_snapshot_scope` keep
  the verb, because what they answer is what the verb promises. A rename of
  those two as well would have cost four contract breaks to fix a collision that
  only two of them are in.
- The enum does not move. It is the wider vocabulary of the two: one field on
  every statement and every path against two tool names. `AGENTS.md`'s rule for
  a tie is that the name a client outside this checkout can see wins. Here both
  are visible, so the count decided it.
- `describe` is the sixth verb and enters `ToolNamingTest`, the only place that
  defines the vocabulary. A `describe` answers for one thing the caller named
  and states what that thing is. A `scope` answers for a source and states the
  boundary of what a caller can ask it.
- The classes follow the names: `Tool\ProjectScope` is `Tool\ProjectDescribe`
  and `Tool\ExtensionScope` is `Tool\ExtensionDescribe`. Every tool class here
  is the CamelCase of its tool name and `Registry` is the only thing that lists
  them. Classes left behind would have been the two-names-for-one-thing the
  rename removes.
- What recorded a call under the former spelling keeps it and gains a line that
  says so. That is the three forward runs and the recorded halves of four tool
  pages, because a recording says what it is of (`D-DOC-006`). The archived
  feedback and the decisions that name the old spelling stay as they are,
  because an entry is evidence with a date on it.

## Assumed

- No client stores a tool name anywhere this server could not reach. The names
  go out at `tools/list` on every connection and
  `bin/typo3-dev-companion update` publishes the skills again. So a stale name
  survives only in a skill file somebody copied by hand.
- That a caller reads the verb at all. Nothing measures it. What the verb buys
  is that two tools with one answer shape cannot have different verbs, which
  `ToolNamingTest::toolsSharingAnOutputSchemaShareTheirVerb` does hold.

## Wrong if

- A later tool wants `scope` for a subject that is neither a source's coverage
  nor a description. An answer about one call's placement, say, which
  `Knowledge\Scope` computes and `typo3_task_guide` already returns as `scopes`.
  Then the verb `scope` is the collision rather than these two names, and what
  has to go is the verb.
- `describe` attracts a tool that should have been `lookup`. The tell is a
  `describe` whose subject the caller does not name, or one where an empty
  result is a legitimate answer. Both of those are the `lookup` shape, and a
  `describe` that can miss is a `lookup` with the wrong verb.
- A session calls typo3_project_scope and gets `Unknown tool`, from a skill
  copied by hand or a client that cached the list. Two of the nine skills open
  on this call. So the failure lands in the first step of the order rather than
  somewhere a session can work around.
