---
id: D-ANS-150
title: A guide says which kind of work it is for
date: 2026-09-04
status: open
coveredBy:
  - ProjectTest::everyGuideSaysWhichKindOfWorkItIsFor
---

# D-ANS-150 — A guide says which kind of work it is for

**Every guide an answer names carries its scope as a field.** A caller who
decides whether to open the page then reads it rather than parses the first
segment of the id.

A session filtered twenty-one guides on the prefix and called
`typo3_rule_lookup` zero times. The filter was right and the read was a regex it
had to write itself.

## Evidence

- `feedback/2026-09-04-053645`. The session says it read `core/` the way it
  correctly read `core/contribution/changelog` and
  `core/contribution/ gerrit-workflow`. The `any/` prefix on the two test guides
  beside them confirmed the conclusion. It asks for the scope "somewhere it is
  asserted rather than implied".
- The scope is already data. `Documents::scopeOf()` returns a `Scope`, declared
  once in the id so a move cannot leave it behind (`D-KNW-058`).
  `guideReference` carried `id`, `title`, `when` and `tool` and not that.
- Which is `AGENTS.md`'s own rule read back: what a reader takes as data lives
  where data lives, and the writer says it once. A caller who takes
  `strtok($id, '/')` writes the regex that rule exists to prevent.

## Decided

- `Schema::guideReference()` gains `scope`, enumerated from
  `Scope::ofKnowledge()`: `core`, `project`, `extension`, `any`. The two answers
  that carry guides render it in the list beside the id.
- **The new file location the feedback asks for fails**, and the reason is the
  page. `core/testing/proving-a-rendering` writes a functional test into
  `typo3/sysext/frontend/Tests/Functional/Rendering/`. It reads the core's own
  functional PHPUnit configuration for whether `echo` survives, and puts a
  fixture class where the core's `autoload-dev` maps it. None of that exists
  outside the core repository, so `core/` is right and the session skipped it
  correctly.
- **Nor is it surfaced for non-core work.** The feedback's fallback rests on the
  same premise, and it would route a project session to a core functional test.
- What the session did is a procedure this repository does not carry for an
  installation you can request over HTTP. That is capture representative URLs
  before the change, capture again after, diff each pair and account for every
  line that moved. That gap is real and stays open: it is a document about
  TYPO3, and this judgement read no checkout for it.
- The `any/testing/browser-check` note does not count as a second finding. The
  session says `typo3_task_guide` named that guide and it read the guide's name
  rather than its `when`. That is the same conclusion this entry repairs one
  level up.

## Assumed

- That a caller who filters a list reads a field it gets over a segment it can
  parse. Nothing measured that; the measurement covers one session that parsed
  the segment when there was no field.
- That the first segment of every document id is a `Scope` case. `scopeOf()`
  answers `uncertain` where it is not, which the enum in the schema does not
  offer. A document filed under a fifth prefix would answer a value the schema
  refuses.

## Wrong if

- A session opens a `core` guide in a project checkout because the field made
  the prefix look decorative, which is the failure this trades against.
- A guide turns out to serve two scopes. Then one value is a claim the page does
  not make, and the split the feedback proposes is the answer after all.
- Nobody reports that they read the field, and the next filter failure is on the
  id again.
