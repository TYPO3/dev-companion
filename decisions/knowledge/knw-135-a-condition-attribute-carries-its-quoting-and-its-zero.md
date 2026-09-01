---
id: D-KNW-135
title: A condition attribute carries its quoting and its zero
date: 2026-08-28
status: open
readings:
  - 2026-09-01
---

# D-KNW-135 — A condition attribute carries its quoting and its zero

**`fluid-conditions-and-arrays` states how a comparison inside a condition
attribute is quoted, and what a bare condition does to a zero.**

The hint is titled after Fluid conditions and said nothing about writing one.
Its four statements were the escaping, the `<f:then>` an `<f:else>` demands, and
two about array literals.

## Evidence

- **The session.** `/home/benji/projects/bootstrap_package` on 2026-08-28,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-28-074011`](../../feedback/archive/2026-08-28-074011-no-hint-covers-quoting-inside-a-fluid-condition.md).
  It reviewed a one-line pull request changing `condition="{cell}"` to
  `condition="{cell}!=""`, and settled the review by running the template
  through two Fluid versions by hand.
- **Measured here on 2026-08-28**, on the three Fluid releases the environments
  below `.environments/` carry — 2.15.0 for 12.4, 4.6.1 for 13.4, 5.3.1 for 14.3
  and for main — with a standalone `TemplateView` and no TYPO3 around it. All
  three answer identically, so the statements take no binding.
- **The double-quoted form does not parse.**
  `You closed a templating tag which you never opened!`, exception 1224485838.
  With the condition on line 2 and the closing tag on line 4, every version
  reports line 4: the message points at the `</f:if>` and not at the condition.
- **The single-quoted comparison works**: `condition="{cell} != ''"` renders the
  then-branch for `"0"` and the else-branch for the empty string.
- **The report's explanation of the zero is wrong**, and the observation is
  right. It says the value is one "PHP reads as falsy".
  `BooleanNode::convertToBoolean()` sends anything `is_numeric()` accepts
  through `(bool)(float)`, so a string of two zeros and a zero written with a
  decimal point are false here while PHP casts both to true.
- **The run also confirmed the statement beside it.** The first harness omitted
  `<f:then>` and the true branch rendered nothing, silently, which is what the
  hint's second statement says.

## Decided

- Two statements rather than one, because a reader arrives at either half on its
  own: the quoting is what a contributor gets wrong, and the zero is why they
  were editing the condition at all.
- **Made in this run.** The evidence is a reading this judgement did, which is
  what `D-FBK-052` bounds the queueing rule to.
- The mechanism is named — `BooleanNode::convertToBoolean()`, `is_numeric()` —
  because the rule is not PHP's and a reader who assumes it is will write the
  next condition wrong the same way.
- Against the report's worked table of four values against two forms. The rule
  states what the table would enumerate, and the corpus is read whole by callers
  who paid for every line of it.
- `appliesTo` gains the words a caller arrives with: the condition attribute,
  the quoting, and the zero that disappeared.

## Assumed

- That the three releases measured stand for every patch level of their lines.
  `convertToBoolean` is the same code on all three, and the parser's attribute
  scan is what it has always been.

## Wrong if

- Fluid changes what an attribute value may contain, and the double-quoted form
  starts parsing. The statement then describes a defect nobody can reproduce.
- A session reports the else branch taken for a value that is not a zero and not
  empty. Then the numeric rule is not the whole of it.
