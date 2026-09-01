---
id: D-ANS-129
title: A list in an answer names the call it raises
date: 2026-09-01
status: open
coveredBy: []
---

# D-ANS-129 — A list in an answer names the call it raises

**Where a describe answer lists something another tool answers about, it names
that tool and the argument to pass, beside the list rather than in a description
nobody re-reads.**

A caller reaches for a tool at a moment of doubt, and the moments that cost this
server's callers most carried no doubt at all.

## Evidence

- One session on 2026-08-31 called 51 of 796 tool calls into this server and
  none into nine of its eighteen tools. Three of the nine cover work it did by
  hand and got wrong: a FlexForm field name found by `grep` and a `python`
  regex, two tables' column order diffed through `SHOW COLUMNS` and read as
  identical, and the site settings read out of the YAML while it was changing
  them.
- The same session filed a correction the next minute retracting its first
  explanation: it had called the client's tool-search six times, so the barrier
  it blamed was one it crossed whenever it wanted the other side. What it named
  instead is the `documents` array on a hint answer, which is how it found the
  phpunit guide — its own best call of the session.
- The pattern is already in this server and unnamed: `icons` names
  `typo3_icon_lookup` in its field description, and the deprecated-files block
  names `typo3_changelog_lookup` in the rendered text.

## Decided

- `typo3_extension_describe` names `typo3_schema_lookup` where it lists the
  tables an extension defines or extends, and `typo3_flexform_lookup` where it
  lists a binding — in the text, which is what the caller reads, and in the
  field description, which is what a client keeps.
- The sentence carries the argument. "This tool exists" is what a tool list
  already says; what a caller in flow is missing is the call itself.
- One sentence per list rather than per entry, and the FlexForm one is a
  constant because two blocks list a binding.
- Against a field in the schema naming the next call. It would be a required
  field on every path for a sentence that is the same every time, and the two
  places a caller reads are the ones written to instead.

## Assumed

- That a caller who did not doubt reads a sentence that arrives unasked. Nothing
  measures this; the `documents` array is the one precedent and it was read by a
  caller already looking for a document.

## Wrong if

- A session reports the describe answer as longer without calling either tool,
  which would say the sentence is size and not a lever — `D-FBK-020` is the
  budget it comes out of.
- The same treatment is asked of every list in every answer, and the answer
  becomes a directory of this server rather than a description of an extension.
