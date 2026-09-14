---
id: D-DOC-068
title: The register this server writes in is copied into its callers' patches
date: 2026-09-09
status: open
coveredBy:
  - KnowledgeTest::aTaskThatWritesFilesIsOwedThePageAboutItsProse
---

# D-DOC-068 — The register this server writes in is copied into its callers' patches

**How this server writes is part of what it hands out. A session writes its
comments in the register it read for the whole task.**

The prose rules here serve the reader who pays per token. A second reader was
never counted: the session that copies the style into a patch a person then
reviews.

## Evidence

- **Three reports from one session**, `/home/benji/projects/typo3-cms`,
  `claude-opus-5[1m]`, on a fix of Forge #107495 into a patch with a new class,
  tests and a `Breaking` entry.
  [`225319`](../../feedback/archive/2026-09-09-225319-no-rule-constrains-the-prose-an-agent-writes.md)
  reports that nothing constrains the prose a patch carries.
  [`225815`](../../feedback/archive/2026-09-09-225815-ruleset-the-server-should-hand-an-agent-for.md)
  writes eleven rules off the sentences its reviewer rejected.
  [`230509`](../../feedback/archive/2026-09-09-230509-the-server-should-apply-its-own-comment-ruleset.md)
  names this server's own surfaces as where those sentences came from.
- **The patch cost one round; its comments cost three.** The reviewer rejected
  the prose twice. Among the rejected: a class docblock of 13 lines with six
  lines of content, and a cross-reference to a position that does not exist. And
  one mechanism explained in the PHP, in the TypeScript and in the test.
- **Five of the eleven rules are this repository's own already.** One point per
  sentence; say what is, not what it is not; a comment that restates the line
  under it is noise. The reason lives in one place; length is a symptom. What
  they are not is in force, and no check counts any of them.
- **Six are new here.** Name the actor. No colon as a reveal. No trailing
  prepositions. No pointer at "above" or "below". Use the codebase's words
  rather than a metaphor invented for the comment. Prefer a longer correct
  sentence to a short broken one.
- **None of the eleven stands stated for the caller at all.**
  `bin/cli prose:check` reads this repository. Nothing below `knowledge/` says
  how a comment in a patch should read, and the core's own `AGENTS.md` carries
  one sentence about when to write one.
- **The report's cost claim does not hold.** It offers six lines of this
  server's with a plain version each and says none is longer. Counted on
  2026-09-09: three are longer, one of them by three times — "A miss is an
  answer" against "An empty result is a result. It means nothing matched, not
  that the search failed", which also breaks the ruleset's own first rule.

## Decided

- **The register is a property of what this server hands out.** An edit to a
  brief, a checklist or a hint counts as something a session will imitate rather
  than only as something it will read.
- **What a caller reads as an instruction comes first.** Sessions copy briefs,
  checklist items and hints; a tool description gets one read at the choice. The
  sweep starts where the imitation happens.
- **The six new rules get their weight one at a time against this corpus**, not
  as a block. Five already stand here and nobody keeps them, which says the gap
  is in the practice rather than in the list.
- **Against the plain versions as offered.** Half of the sample costs more
  tokens, and one of them breaks a rule the same report asks for. A line gets a
  judgement rather than a substitute.
- **A rule for the caller is a separate piece of work from one in force here.**
  The first states what a patch's prose owes and lands below `knowledge/`; the
  second is a sweep over this repository. Neither waits for the other.
- Taken on rather than closed here. The caller's half states something and ships
  it; the sweep touches every surface this server describes itself with.

## Assumed

- That the imitation is the mechanism and not the model's own habit. Three
  reports from one session say so about that session. Nothing here measures it
  across sessions, and the same session also called its own first drafts
  careless.
- That a reviewer on review.typo3.org reads these comments and pays for them.
  Stated by the report and consistent with what a core review is, not measured.

## Wrong if

- A session that works from plainer briefs writes comments in the same register.
  Then the register is the model's and this entry names the wrong cause.
- The sweep costs the `instructions` or a description its budget, and the
  measurements `D-ANS-004` and `D-SKL-026` made are what decides. Then plain
  words and reach are in tension and the tension is the finding.
- A report says a reviewer asked for the compressed form back. Then the two
  readers want different things and the caller's rule has to say which one it
  serves.

## Since then

The caller's half shipped as `any/writing/the-prose-a-patch-carries`, and every
brief for a task that writes files names it. The sweep followed the same day
across all four surfaces. 22 of 143 checklist items, 8 of 342 hint lines, and
the two "The trap:" labels in the triage checklist. And two of the three
description lines the report quoted.

The corpus was better than the sample suggested, and a mechanical pass would
have been wrong. 16 sentences end on a preposition, most of them ordinary
relative clauses. Half of the 50 that open on a colon introduce a list or a
definition. "A miss is an answer" stayed, because the sentence under it explains
it and the replacement was three times as long.

`D-DOC-069` is the judgement this entry asked for. Three of the six hold here,
one folded into the vocabulary rule, and two stay out.
