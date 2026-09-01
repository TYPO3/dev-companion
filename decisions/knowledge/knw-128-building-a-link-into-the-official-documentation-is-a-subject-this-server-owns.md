---
id: D-KNW-128
title: Building a link into the official documentation is a subject this server owns
date: 2026-08-27
status: open
readings:
  - 2026-09-01
coveredBy: []
---

# D-KNW-128 — Building a link into the official documentation is a subject this server owns

**A link into docs.typo3.org is a permalink identifier with the running version
appended, and the corpus states the template side and the PHP side in one
entry.**

A session replacing hardcoded anchors in install-tool templates established the
whole of it from the checkout, and reports that the server said nothing about
which core ViewHelper renders such a link.

## Evidence

- The probe misses. `bin/cli hints:probe` with the feedback's own two queries
  reached `fluid-templates`, `fluid-layouts-sections`, `fluid-viewhelpers` and
  `upgrade-wizards`. None of them says which ViewHelper builds a documentation
  link.
- Nothing below `knowledge/`, `skills/` or `documentation/` carried
  `DocumentationViewHelper`, `getDocsLink`, `Typo3Information` or
  `link.documentation` before this change.
- Half of the feedback's own suggestion was already answered. `fluid-templates`
  states that only a few prefixes are registered globally and that the backend
  one is declared per template as
  `xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers"`, and names
  `typo3_fluid_namespace_list` as what answers the first half for an
  installation. The session grepped `Configuration/Fluid/Namespaces.php` instead
  of calling it, which is a tool that exists, is named in the corpus and
  describes itself — nothing in the corpus can close that.
- The claim about TYPO3 holds, read on all four covered branches.
  `Backend\ViewHelpers\Link\DocumentationViewHelper` is present on 12.4, 13.4,
  14.3 and main, extends `AbstractTagBasedViewHelper`, takes one required
  `identifier` argument and renders an `a` element.
- `render()` calls `addAttribute('target', '_blank')` and
  `addAttribute('rel', 'noreferrer')` unconditionally on all four. The core's
  own functional test for the ViewHelper renders it with
  `class="baz" target="_self" rel="nofollow"` and asserts
  `class="baz" target="_blank" rel="noreferrer"`, so the two are forced and
  `class` is not.
- The identifier guard is one exception with one code across the four:
  `InvalidArgumentException` 1728643940 on an identifier containing `@`, thrown
  in the ViewHelper on 12.4 and in `Typo3Information::getDocsLink()` above it.
- The PHP side is where the branches differ. 12.4 has no `getDocsLink()` at all
  and the ViewHelper builds the URL from its own `$urlPattern` and
  `Typo3Version::getBranch()`; 13.4 has it as an instance method; 14.3 and main
  have it static, with a comment saying the static form is what lets an early
  message build a link before DI is ready. `Typo3Information` is not `@internal`
  on any of them.
- The core writes it into user-facing messages rather than only into templates:
  a `PackageManager` deprecation message, the `PAGEVIEW` and `FLUIDTEMPLATE`
  content objects, and the install tool's opcode-cache environment check.
- The failsafe claim holds on all four. `ViewHelperResolver`'s
  `FailsafeContainer` branch builds the ViewHelper through
  `GeneralUtility::makeInstance()`, `install/Classes/ServiceProvider.php` wires
  four ViewHelpers and `DocumentationViewHelper` is not among them, and five
  install-tool templates use `be:link.documentation` on 13.4 and above.
- The class carries `@internal not part of TYPO3 Core API.` on all four, which
  the feedback did not report and which decides what an extension may be told.
- The statements fixed the miss. `documentation-links` is first on both of the
  feedback's queries afterwards, at `appliesTo(23) + text(141)` and
  `appliesTo(18) + text(168)`, and the four Fluid entries keep the answers they
  had.

## Decided

- **Step 1a, closed on the spot.** The reading was made in this run against all
  four checkouts, and queueing it would send the next session to the same files
  —
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md).
  Nothing in `src/`, no declared schema and no skill contract moves.
- One entry rather than two, `documentation-links` in
  `knowledge/hints/documentation.json`, in the `docs`, `fluid` and `php`
  domains. The subject is one — how such a link is built — and splitting it
  would put the template half in `fluid-templates` and leave the PHP half with
  no home at all: `php.json` holds the language and the contribution process,
  not core APIs, and the other two `docs` entries are about writing
  documentation rather than linking to it.
- Unscoped, with the `@internal` marking stated instead. An extension's backend
  module asks the same question and the honest answer is the mechanism together
  with the missing promise; `scope: "core"` would withhold both.
- The identifier, the appended branch, the forced attributes and the failsafe
  route are unbound, because they hold on all four branches. Only the PHP call
  is bound, in three statements: absent `until: 12`, an instance method at
  `since: 13, until: 13`, static `since: 14`.
- **No routing line and no pointer from `fluid-templates`.** The entry ranks
  first on both queries on its `appliesTo` alone, so either would be a second
  place to keep true for nothing.
- The half of the feedback about `typo3_fluid_namespace_list` being passed over
  is **not answered**, and there is nothing to answer: the tool exists, the
  corpus names it, and which tool a session reaches for is not something this
  server can decide from here.

## Assumed

- That the `@internal` sentence is what an extension author needs rather than a
  withholding. The core marks the class internal and ships no other way to
  render such a link, so stating the mechanism and the missing promise was read
  as more useful than stating neither.
- That `documentation-links` earns its rank across the corpus. It was measured
  on the two queries that missed and on four controls, not on the whole corpus.

## Wrong if

- A session writes a documentation link with the statement in reach and still
  hardcodes the anchor. Then it is placed where the task does not pass, and the
  lever is delivery rather than the corpus.
- The entry pulls into answers about *writing* documentation. Its text carries
  "documentation" throughout, and the control query
  `document my site package extension manual` already ranks it second on text
  alone — a query about a changelog or an extension manual losing a slot to it
  is the cost.
- An extension is told the ViewHelper is off limits and writes the URL out by
  hand, losing the version suffix. Then the `@internal` sentence reads as a
  prohibition rather than as the absent promise it is.
- The permalink identifier scheme changes, or `docs.typo3.org` stops appending
  `@<branch>`. Then the first statement is wrong on every branch at once and no
  version boundary catches it.
