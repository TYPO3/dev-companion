---
id: D-ANS-150
title: A guide says which kind of work it is for
date: 2026-09-04
status: open
coveredBy:
  - ProjectTest::everyGuideSaysWhichKindOfWorkItIsFor
---

# D-ANS-150 — A guide says which kind of work it is for

**Every guide an answer names carries its scope as a field.** A caller deciding
whether to open the page then reads it rather than parsing the first segment of
the id.

A session filtered twenty-one guides on the prefix and called
`typo3_rule_lookup` zero times. The filtering was right and the reading was a
regex it had to write itself.

## Evidence

- `feedback/2026-09-04-053645`. The session says it read `core/` the way it
  correctly read `core/contribution/changelog` and `core/contribution/
  gerrit-workflow`, and that the `any/` prefix on the two neighbouring testing
  guides confirmed the reading. It asks for the scope "somewhere it is asserted
  rather than implied".
- The scope is already data. `Documents::scopeOf()` returns a `Scope`, declared
  once in the id so a move cannot leave it behind (`D-KNW-058`), and
  `guideReference` carried `id`, `title`, `when` and `tool` and not that.
- Which is `AGENTS.md`'s own rule read back: what is read as data lives where
  data lives and is said once. A caller taking `strtok($id, '/')` is writing the
  regex that rule exists to prevent.

## Decided

- `Schema::guideReference()` gains `scope`, enumerated from
  `Scope::ofKnowledge()` — `core`, `project`, `extension`, `any` — and the two
  answers that carry guides render it in the listing beside the id.
- **The re-filing the feedback asks for is refused**, and the reason is the
  page. `core/testing/proving-a-rendering` writes a functional test into
  `typo3/sysext/frontend/Tests/Functional/Rendering/`, reads the core's own
  functional PHPUnit configuration for whether `echo` survives, and puts a
  fixture class where the core's `autoload-dev` maps it. None of that exists
  outside the core repository, so `core/` is right and the session skipped it
  correctly.
- **Nor is it surfaced for non-core work.** The feedback's fallback rests on the
  same premise, and it would route a project session to a core functional test.
- What the session actually did — capture representative URLs before the change,
  re-capture after, diff each pair and account for every line that moved — is a
  procedure this repository does not carry for an installation you can request
  over HTTP. That gap is real and is left open: it is a document about TYPO3,
  and this judgement read no checkout for it.
- The `any/testing/browser-check` note is not taken as a second finding. The
  session says `typo3_task_guide` named that guide and it read the guide's name
  rather than its `when`, which is the same reading this entry fixes one level
  up.

## Assumed

- That a caller filtering a listing reads a field it is given over a segment it
  can parse. Nothing measured that; what is measured is one session parsing the
  segment when there was no field.
- That the first segment of every document id is a `Scope` case. `scopeOf()`
  answers `uncertain` where it is not, which the enum in the schema does not
  offer — a document filed under a fifth prefix would answer a value the schema
  refuses.

## Wrong if

- A session opens a `core` guide in a project checkout because the field made
  the prefix look decorative, which is the failure this trades against.
- A guide turns out to serve two scopes, so one value is a claim the page does
  not make and the split the feedback proposes is the answer after all.
- Nobody reports reading the field, and the next filtering failure is on the id
  again.
