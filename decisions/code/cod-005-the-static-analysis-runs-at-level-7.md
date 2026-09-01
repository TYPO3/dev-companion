---
id: D-COD-005
title: 'The static analysis runs at level 7'
date: 2026-08-23
status: open
readings:
  - 2026-09-01
coveredBy: []
---

# D-COD-005 — The static analysis runs at level 7

**`phpstan.neon` says level 7: what a library hands back is narrowed where the
code already narrows it, and what a shape crosses is written down once.**

The file said level 6 from the day it was written and nothing recorded why that
number rather than another.

## Evidence

- 59 findings with the level raised, read on 2026-08-23: 29 in `src/` and 30 in
  `tests/`. None was a failing test. The six that could have crashed something
  were taken out the day before and are not in that count.
- Four kinds, and each wanted a different answer. A library union the code
  narrows by knowing better — `DOMNode` against `DOMElement` seven times,
  `curl_setopt_array`, `proc_open`, the two schema shapes `mcp/sdk` declares. A
  decode at a network boundary — `Forge::api()`, three in `Gerrit`,
  `Hints::load()`. A capture group the analysis cannot see is always there —
  four in `Wrap`, one in `Server\Installer`, one in `Tool\ChangelogLookup`. And
  `max()` over a list that is not empty in context.
- What the fixes came to is smaller than the count. `@phpstan-type Hint` says
  the corpus shape once where three signatures repeated it;
  `Documentation::elements()` and `first()` answer with elements or nothing and
  took `?: []`, an `instanceof` and two `false` checks out of four call sites;
  `Recent::held()` hands back what a caller put in and each of the four says
  which shape that is.
- Two are behaviour rather than annotation. `Fetch::read()` answers a URL nobody
  composed as a read that did not happen, at the one door this server reads a
  host through; the reader of a todo's labelled head puts a pair back rather
  than appending through an offset, which is what lost the pair.

## Decided

- The level is 7 and the promise is about every file written after it, which is
  what a level is for. 30 of the 59 were in `tests/` where the answer is mostly
  an annotation, and that is the price rather than an argument against it.
- Nothing is baselined and no error is ignored. A baseline is a second file
  saying what is exempt, and it is what makes a level stop being read.
- A test states its demand with `self::fail()` where an assertion cannot narrow.
  `assertSame(1, preg_match(...))` leaves the offsets unknown, so what holds the
  match is `if (preg_match(...) !== 1) { self::fail(...); }` — the same demand,
  and the type follows from it.
- Per-path levels were rejected. Holding `src/` at 7 and `tests/` at 6 buys the
  29 without the 30, at the price of a second level to explain and keep.
- `coveredBy: []`, because what holds this is the analysis itself:
  `composer stan`, and `composer ci` before every commit.

## Assumed

- That the narrowings are the ones the code performs rather than ones written to
  quiet the analysis. Each was read at its site, and the two inline `@var` in
  `Server\Factory` are the ones nothing else checks — `tests/Contract/` holds
  the tools to the schemas they declare, and the SDK's reading of the same
  shapes is what that block states.
- That level 8 is a different question. It is about null, not about shapes, and
  nothing here says it should follow.

## Wrong if

- A change is written to satisfy the level rather than the reader: a narrowing
  that hides a case instead of stating it, or a `@var` where the honest answer
  is a branch. The two in `Server\Factory` are where that would start.
- A session's first hour on a new tool goes on annotations. Then 7 is the wrong
  promise for `tests/`, and the per-path level rejected above is what the
  evidence would be for.
- `Fetch::read()` answering an empty URL as a read that did not happen hides a
  composing bug that a failed request would have shown.
