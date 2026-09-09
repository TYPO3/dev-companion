---
id: D-DOC-068
title: The register this server writes in is copied into its callers' patches
date: 2026-09-09
status: open
coveredBy: []
---

# D-DOC-068 — The register this server writes in is copied into its callers' patches

**How this server writes is part of what it hands out, because a session writes
its comments in the register it spent the task reading.**

The prose rules here were written for the reader who pays per token. A second
reader was never counted: the session that copies the style into a patch a
person then reviews.

## Evidence

- **Three reports from one session**, `/home/benji/projects/typo3-cms`,
  `claude-opus-5[1m]`, fixing Forge #107495 into a patch with a new class, tests
  and a `Breaking` entry.
  [`225319`](../../feedback/2026-09-09-225319-no-rule-constrains-the-prose-an-agent-writes.md)
  reports that nothing constrains the prose a patch carries,
  [`225815`](../../feedback/2026-09-09-225815-ruleset-the-server-should-hand-an-agent-for.md)
  writes eleven rules off the sentences its reviewer rejected, and
  [`230509`](../../feedback/2026-09-09-230509-the-server-should-apply-its-own-comment-ruleset.md)
  names this server's own surfaces as where those sentences came from.
- **The patch cost one round; its comments cost three.** The reviewer rejected
  the prose twice. Among the rejected: a class docblock of 13 lines carrying six
  lines of content, a cross-reference to a position that does not exist, and one
  mechanism explained in the PHP, in the TypeScript and in the test.
- **Five of the eleven rules are this repository's own already.** One point per
  sentence; say what is, not what it is not; a comment that restates the line
  under it is noise; the reason lives in one place; length is a symptom. What
  they are not is kept, and no check counts any of them.
- **Six are new here.** Name the actor. No colon as a reveal. No trailing
  prepositions. No pointing at "above" or "below". Use the codebase's words
  rather than a metaphor invented for the comment. Prefer a longer correct
  sentence to a short broken one.
- **None of the eleven is stated for the caller at all.** `bin/cli prose:check`
  reads this repository; nothing below `knowledge/` says how a comment in a
  patch should read, and the core's own `AGENTS.md` carries one sentence about
  when to write one.
- **The report's cost claim does not hold.** It offers six lines of this
  server's with a plain version each and says none is longer. Counted on
  2026-09-09: three are longer, one of them by three times — "A miss is an
  answer" against "An empty result is a result. It means nothing matched, not
  that the search failed", which also breaks the ruleset's own first rule.

## Decided

- **The register is a property of what this server hands out**, and an edit to a
  brief, a checklist or a hint is weighed as something a session will imitate
  rather than only as something it will read.
- **What a caller reads as an instruction comes first.** Briefs, checklist items
  and hints are copied; a tool description is read once while choosing. The
  sweep starts where the imitation happens.
- **The six new rules are weighed one at a time against this corpus**, not
  adopted as a block. Five are already written here and unkept, which says the
  gap is in the keeping rather than in the list.
- **Against taking the plain versions as offered.** Half of the sample costs
  more tokens, and one of them breaks a rule the same report is asking for. A
  line is judged rather than substituted.
- **A rule for the caller is a separate piece of work from keeping one here.**
  The first states what a patch's prose owes and lands below `knowledge/`; the
  second is a sweep over this repository. Neither waits for the other.
- Taken on rather than closed here. The caller-facing half states something and
  ships it; the sweep touches every surface this server describes itself with.

## Assumed

- That the imitation is the mechanism and not the model's own habit. Three
  reports from one session say so about that session; nothing here measures it
  across sessions, and the same session also called its own first drafts
  careless.
- That a reviewer on review.typo3.org reads these comments and pays for them.
  Stated by the report and consistent with what a core review is, not measured.

## Wrong if

- A session working from plainer briefs writes comments in the same register.
  Then the register is the model's and this entry names the wrong cause.
- The sweep costs the `instructions` or a description its budget, and the
  measurements `D-ANS-004` and `D-SKL-026` made are what decides. Then plainness
  and reach are in tension and the tension is the finding.
- A reviewer is reported asking for the compressed form back. Then the two
  readers want different things and the caller-facing rule has to say which one
  it serves.
