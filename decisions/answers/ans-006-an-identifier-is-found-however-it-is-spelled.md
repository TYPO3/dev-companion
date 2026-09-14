---
id: D-ANS-006
title: An identifier is found however it is spelled
date: 2026-08-02
status: open
coveredBy:
  - PackageSourcesTest::anIdentifierReachesTheEntryTitledInWords
---

# D-ANS-006 — An identifier is found however it is spelled

**The one matcher behind the label search and the changelog search also compares
a query term with `_`, `.` or `-` separator-free, on both sides.**

`ext_tables.php` has that spelling where somebody uses it and the spelling
`ExtTablesPhpInExtensions` in a title. So the caller's own name for the thing
reached neither the file name nor the words derived from it.

## Evidence

- Six shapes reported from two checkouts by `feedback/2026-07-31-194504-…` and
  by `feedback/2026-07-31-172753-…`: `ext_tables.php`, `ext_emconf.php`,
  `list_type`, `backend_layout`, `SC_OPTIONS` and `mod.web_layout`. Every one
  has an entry in `.checkouts/14.3` and every one reached nothing; all six reach
  their entry now.
- The multi-word queries those sessions typed still reach nothing, and that is
  the documented rule rather than the defect. The miss now names
  `"list_type" reaches 1`, `"sc_options" reaches 1`,
  `"mod.web_layout" reaches 1`. That is the next step it could not print while
  those terms reached nothing either.

## Decided

- In `LabelSearch::carryingEvery()`, which both searches go through. `D-DIS-003`
  and `R-ANS-004` exist against a second matcher for the changelog. One rule
  serves both, and the answer means what the description says.
- The matcher compares only a term that carries a separator that way. A query
  without one behaves exactly as it did, and nothing this matched before matches
  less.
- Not by a split of the term into words. `labels.save_document` is one thing a
  caller asks for. Three terms joined by "carries every" would also match a
  label that happens to carry the three words apart.

## Assumed

- The separators removed from the haystack too make the comparison work. That
  lets a term span what were two words there: `esphp` reaches `ExtTablesPhp`.
  Only a term that already carries a separator can reach that. So the cost has a
  bound: how rarely such a term is also a fragment of something else.

## Wrong if

- A query that names one identifier comes back with entries about another. The
  separatorless form of the term is a fragment of an unrelated title.
- The label search starts to answer more widely than its callers expect. The
  same rule now reaches a label whose id spells the query's identifier apart.

## Since then

The other half of what those two feedback asked for is a filter rather than a
wording: the lookup takes a tag now. One tag says which deprecations the
Extension Scanner has a matcher for and another which system extension a change
is in. No wording reaches that number, because the tags are inside the files
rather than in the names the search reads. That is why it is a field: the filter
reads what the version and the type narrowed to, in a fortieth of the time.

What the corpus does not have is an extension key of the caller's own. The tags
name the system extension a change is in, never the package it affects. A miss
says which tags exist rather than leaves the caller to guess.
