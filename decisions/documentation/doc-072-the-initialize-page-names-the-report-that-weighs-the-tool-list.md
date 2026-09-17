---
id: D-DOC-072
title: The initialize page names the report that weighs the tool list
date: 2026-09-17
status: open
coveredBy:
  - ToolSurfaceTest::everyPageIsWhatTheServerDeclares
---

# D-DOC-072 — The initialize page names the report that weighs the tool list

**`documentation/server/initialize.rst` shows the `initialize` result, the
instructions, the prompts and the tool list as a count. `bin/cli tools:index`
reads all of it from a session it runs against `Factory::create()` in its own
process. What the list weighs is `bin/cli tools:measure`'s, and the page names
the command in place of the number.**

`D-DOC-071` put the bytes per member on the page, and its second **Wrong if**
fired the same day. Every edit to a description or a schema moved the number,
and the move was a diff in a page the edit had not meant to touch.

## Evidence

- The weight moved four times on 2026-09-17, from 311,603 bytes through 311,670
  and 312,004, once per edit to `CommitMessageGuide`'s input schema. Each
  regeneration was a diff that carried no information a reader of the page had
  asked for.
- `bin/cli tools:measure` already printed the same sums, per tool and over all
  of them, with the note on which half a client hands the model. So the page
  carried a copy of a report, and `D-DOC-009` says a copy of a count goes stale
  where the report does not.
- The count of tools stays. It moves when a tool arrives, and that regenerates
  the tool pages and the index in the same commit, so the page's diff is the
  change a reader expects.
- The generators wrote reStructuredText through the markdown wrap, and the first
  sentence with a literal at a line's end showed it. `Wrap::text()` broke
  ``bin/cli tools:measure`` over the line, and `prose:format` joined it back, so
  `tools:index` and the formatter rewrote each other. `Wrap::rstText()` keeps
  the literal whole, and both generators wrap through it.

## Decided

- The page keeps everything `D-DOC-071` decided but the bytes. It stands as a
  standing page of `ToolSurface`, so `tools:index` writes it, `tools:check`
  reports it and `ToolSurfaceTest` fails on it.
- The tool list paragraph is the count and the members of an entry, and then the
  command that measures the weight. No byte count reaches the page.
- A generator that writes reStructuredText wraps through `Wrap::rstText()`, so
  what it writes is what `prose:format` leaves alone.

## Wrong if

- A reader of the page wants the weight and the command is not where they look.
  Then the page carries a rendered report the way `decisions:index` renders a
  listing, dated, and regenerated on purpose rather than on every edit.
- A generated page picks up another count that moves with an edit elsewhere.
  Then the rule belongs in `ToolSurfaceTest` rather than in a decision, as a
  check over the standing pages for a number that is not a tool count.
