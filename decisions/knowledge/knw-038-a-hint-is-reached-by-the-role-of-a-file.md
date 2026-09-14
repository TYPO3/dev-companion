---
id: D-KNW-038
title: 'A hint is reached by the role of a file'
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aFileBelowAnExtensionIsAnsweredByItsRole
  - HintsTest::anExtbasePluginHasAHintOfItsOwn
  - HintsTest::everyHintIsReachedByItsOwnTitle
---

# D-KNW-038 — A hint is reached by the role of a file

**The bare `extbase` pattern gives way to phrases that pair the key with a role.
So a path below `sysext/extbase/` reaches the family by its role alone.**

An `appliesTo` pattern matches in the paths as well as in the task, so a bare
extension key matches every file in that extension. A core bugfix with
`typo3/sysext/extbase/Classes/Service/ImageService.php` got the Extbase brief
for a file that resolves files and builds URLs.

## Evidence

- The report is the feedback of 2026-08-02. Thirteen plugin statements,
  registerPlugin, FlexForm, the paginators, cacheHash, as the largest block of
  the answer, against the two lines that bore on the task. `D-KNW-032` has since
  split those into five hints, which left the entry hint alone still reached by
  the bare key.
- `Hints::find` on that path alone, before: `system-extension-boundaries`,
  `dependency-injection`, `extbase` at `appliesTo(7) text(0)` — the whole match
  was the directory name. After: the first two, and nothing from the family.
- The report's own query, both paths and the task: `fluid-viewhelpers` stays
  first at `appliesTo(30)` and `extbase` is gone from the answer.
- `packages/my_ext/Classes/Domain/Repository/ProductRepository.php` still
  reaches `extbase-domain-mapping` at `appliesTo(36)`, and
  `Configuration/TCA/Overrides/tt_content.php` still reaches
  `extbase-plugin-registration` at `appliesTo(42)`. Those are role patterns and
  are what should find the family.
- `bin/cli hints:coverage` prints the same output before and after, to the
  character. It counts what nothing reaches, and everything reaches a hint that
  answers everything.
- The sweep over every bare single-word pattern in the corpus against the sysext
  keys of `.checkouts/` finds three: `extbase`, `impexp` on `impexp-artifact`,
  `styleguide` on `css-styleguide-demos`.

## Decided

- The bare token goes rather than a services set out of the block. The split the
  feedback asked for is `D-KNW-032`, and what it left is one orientation hint of
  two statements. There is no service statement in the corpus to move into a set
  of its own. One written to justify the split would be knowledge nobody asked
  for.
- What replaces it names a role or a decision. `use extbase`, `using extbase`,
  `need extbase`, `extbase plugin`, `extbase extension`, `extbase controller`,
  `without extbase`. A multi-word pattern needs its words adjacent, so none of
  them occurs in a path.
- `impexp` and `styleguide` keep their bare key. There the extension is the
  subject of the hint and the key is the only name that subject has. So a file
  below it is about it. That is exactly what `extbase`, a framework whose files
  carry a dozen roles, is not.
- The entry hint keeps its id, per `D-KNW-032`, and is now reached by its own
  title through its body alone: `appliesTo(0) text(241)`.

## Assumed

- The body text serves a caller who types `extbase` and nothing else. The
  one-term query has cover whole, which is the `coversEveryTerm` way past the
  floor. It reaches the entry hint and three more of the family, no longer first
  among them.
- Those three are the whole shape today. Nothing keeps a new hint from a second
  introduction of it, and the sweep ran by hand.

## Wrong if

- A question that names Extbase in a form nobody anticipated comes back without
  the entry hint. That would mean the patterns narrowed past the questions they
  answer.
- A path below `sysext/extbase/` whose role really is a plugin or a domain
  concern reaches nothing. That would mean the role patterns are absent rather
  than the key redundant.
- Another hint gains a bare extension key and nothing says so, because coverage
  cannot see it and the sweep is not a check.
