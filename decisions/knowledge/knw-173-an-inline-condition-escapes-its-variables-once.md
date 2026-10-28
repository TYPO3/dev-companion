---
id: D-KNW-173
title: An inline condition escapes its variables once
date: 2026-10-28
status: open
coveredBy:
  - HintsTest::anInlineConditionInAnAttributeIsToldWhereItsEscapingHappens
---

# D-KNW-173 — An inline condition escapes its variables once

**The Fluid hints state where an inline `f:if` escapes, because a session read
a correct escape as a defect and asked for a workaround.**

## Evidence

- [`feedback/2026-10-26-160550`](../../feedback/archive/2026-10-26-160550-no-answer-on-where-a-pageview-header-reads-the.md)
  came from a sitepackage header on 14.3. It reported that a string an inline
  `f:if` built in an attribute came out escaped. It asked for a hint that sends
  the reader to a partial or to block `f:if`.
- The claim does not reproduce. A probe rendered fourteen templates with
  standalone Fluid at `2.15.0`, `4.6.1` and `5.3.2` from `.checkouts/fluid`.
  Those are the lines 12.4, 13.4 and 14.3 pin. A variable inside `then` came out
  escaped exactly once in a plain HTML attribute, in a tag-based ViewHelper
  attribute, in a declared argument and in the body. A literal in `then` came
  out raw. A block `<f:if>` in the attribute rendered the same string.
- `AbstractConditionViewHelper` registers `then` and `else` as escaped
  arguments and sets `escapeOutput` to false. `TemplateParser::parseArguments()`
  turns the escaping off for an argument that is not declared escaped, so a
  tag attribute is escaped once by the `TagBuilder`.
- TYPO3 registers no Fluid interceptor on 12.4, 13.4, 14.3 or `main`.
  `f:link.typolink` hands `class` and `title` to `LinkFactory`, and `LinkResult`
  escapes them once through `implodeAttributes()`. That half was read in
  `.checkouts/14.3` and not run.
- `bin/cli hints:probe` on the feedback's query reached
  `fluid-conditions-and-arrays`, which said only that Fluid escapes output.

## Decided

- **Step 1a of the ladder, with the knowledge corrected rather than copied.**
  The hint states the behaviour the probe measured, and the query now reaches
  it first.
- **Rejected: the suggested workaround.** A partial or a block `f:if` renders
  the same string, so the hint would send a reader to a rewrite that changes
  nothing.
- The raw literal stays in the statement. It is the one case the probe found
  where the output is wrong, and the session could have met that one instead.

## Assumed

- That the session saw an `&amp;` or a `&quot;` from a value in the string. The
  feedback quotes no output, so the case it met is a reconstruction.

## Wrong if

- A session on a covered line shows an inline `f:if` that escapes a value twice,
  with the template and the output.
- A TYPO3 ViewHelper that takes such an attribute escapes it before it hands it
  to a builder that escapes again.
