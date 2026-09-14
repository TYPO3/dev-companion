---
id: D-ANS-046
title: 'A manual result covers the question it is returned for'
date: 2026-08-03
status: open
coveredBy:
  - DocumentationTest::aResultCoversTheQueryItIsKeptFor
  - DocumentationTest::theAnswerSaysWhereNothingCoversHalfTheQuery
---

# D-ANS-046 — A manual result covers the question it is returned for

**The live-manual search keeps what covers the query, the way the rule search
already does. Where nothing clears that floor, the answer is a miss rather than
the best six collisions. It offers the largest part of the query that still
reaches, and names the changelog for what the manual demonstrates and never
states.**

Six pages came back `answered`, each matched on one word of a four- to six-word
question. The session read the match line, described the rank correctly, and
went to its vendor tree for the answer.

## Evidence

- `feedback/2026-08-03-164734` re-run on 2026-08-03 against
  `DocumentationLookup::answer()` in this worktree, `targetVersion: 14`. All six
  results reproduce, in the order reported and with the same `matched` fields.
  The account of what came back is accurate to the page.
- Its account of the mechanism is accurate in the part that decides the rank and
  wrong in one word. The match is not substring. `TermSearch` reduces a query
  word to a stem and looks for it at a word boundary. That is why `templa` and
  `overri` are what the answer reports.
- Asked one query at a time at `limit: 10`,
  `layout root paths login screen override` ranks *LoginProvider* ninth. That is
  out of the 1419 pages the four manuals index at 14.3. Each of the six above it
  carries exactly one of the six terms — `path`, `root`, `overri`, and `layout`
  three times.
- *LoginProvider* is the page that answers that query.
  `ApiOverview/Backend/LoginProvider.html` says extensions extend the template,
  layout and partial lookup paths in the provider, and that the template must
  use `<f:layout name="Login">`. It is reachable: `login provider` returns it
  first, `login screen layout` fifth, `login form template` seventh.
- The other two queries have no page to reach. TYPO3 Explained 14.3 writes
  `.fluid.html` 49 times and every one is the file-name caption of a code
  example. Read as one file, the whole manual carries no `fluid-rename` and one
  `Fluid 5` inside a version table. It carries no sentence about a file
  extension for templates. The session read `UsingFluidInTypo3.html`,
  `Fluid/Versions.html`, `Fluid/Introduction.html` and both `FileStructure`
  pages whole.
- What answers them is already on this disk.
  `.checkouts/14.3/typo3/sysext/core/Documentation/Changelog/14.0/Feature-108166-FluidFileExtensionAndTemplateResolving.rst`
  states the new extension, that it is entirely optional, and the fallback chain
  per template path. That is the order the session settled with a read of
  `TemplatePaths::resolveFileInPaths()` in its vendor tree.
- The changelog matcher reaches that entry in one call. Over the 3766 entries
  `.checkouts/14.3` ships, `fluid file extension` returns `Feature-108166` and
  nothing else, and so does `fluid template resolving`.
- Nothing separates these six results from a hit. `Documentation::lookup()`
  keeps every page with a score above zero and returns the top `limit`.
  `Documents::search()` drops a section that covers less than half the query's
  weight. The coverage is there: `TermSearch::score()` returns it as its second
  value and `lookup()` discards it in the destructuring.
- Measured over the same 1419 pages at the rule search's own `MIN_COVERAGE` of
  0.5. `Fluid template file naming convention v14` and
  `layout root paths login screen override` clear it nowhere, and
  `fluid.html file extension templates` clears it once. The queries behind the
  entries above survive. `record objects` clears it 15 times with *Record
  objects* at 1.00, and `login provider` 3 times with *LoginProvider* at 1.00.
- `Search\Subsets::largestReaching()` over the same index and the same matcher
  offers `layout login` for the third query. For the second it offers
  `naming conven` and `fluid file` among four.
- `bin/cli feedback:list` on 2026-08-03: 25 open in four directories. This shape
  is in three of them. `2026-08-01-002928` sits in the archive behind
  `D-ANS-021`, and `2026-08-01-003103` asks for it as its suggestion (b). This
  one's sibling `2026-08-03-164805` reports that the base skill routes "is this
  still current" to this tool, where "the routing exists but terminates
  nowhere".

## Decided

- **`D-ANS-021`'s first **Wrong if** fired, and the lever it named is not the
  one.** That entry expected the index itself to be next, and page bodies to be
  what a table of contents cannot carry. Two of these three queries have no page
  body to match either. The manual shows the new file extension in 49 captions
  and explains it nowhere, so body text would have returned 49 pages that answer
  nothing.
- **The suggestion fails on its premise and stands on what it is after.** Its
  second half is the one this evidence supports: where nothing clears a
  threshold, say so rather than return the best six collisions.
- **Queued rather than closed on the spot.** The floor changes what
  `typo3_documentation_lookup` returns and the miss changes its answer shape,
  which [judging.md](../../documentation/records/judging.rst) puts on the
  reviewed side of the autonomous line.
- **`normal`, and the corpus is what set it.** Four reports from three checkouts
  describe this tool as it answers confidently with unrelated pages, and one of
  them is the archived feedback behind `D-ANS-021`. A card nobody judged arrives
  at `low`; this one is not that.
- **The floor and the miss are one change rather than two.** A floor without a
  miss turns six wrong pages into an empty answer with nothing in it, which is
  the state `D-ANS-043` stands against.
- **The corpus sentence points at the changelog, and it is the mirror of
  `D-ANS-043`.** There a changelog miss names the manual; here the manual
  documents a version boundary nowhere and the changelog carries it whole.
- **This entry writes no new requirement.** `R-ANS-006` already says a miss
  names what there would have been to find and `R-ANS-018` that an absence names
  the tool that has it. What this lookup lacks is a miss to hang either on. The
  commit that sets the floor writes what must hold about it, beside `R-DOC-002`.

## Assumed

- That a floor exists that drops these and keeps what the tests already hold.
  Measured against the live 14.3 index above. The fixtures in
  `DocumentationTest` are corpora of a few pages. There a term's weight and a
  page's dilution do not behave as they do over 1419.
- That a caller told the manual documents something nowhere asks the changelog.
  `D-ANS-043` assumed the same about its corpus sentence, and nothing here
  measures it either.
- That the subsets are worth an offer on this corpus. On the third query the
  largest one is `layout login`, which reaches a single event page rather than
  *LoginProvider*. So the offer buys a shorter query rather than the answer, and
  the shorter query is what `D-ANS-021` established reaches.
- That the manual stays this way. A page that states the `.fluid.html`
  convention is one commit away upstream. It would move two of these three
  queries from a routing answer to a ranked one.

## Wrong if

- A session follows the offered subset or the named corpus and reports the same
  end: the question settled with a read of installed source. Then a miss is
  worth less than this entry and `D-ANS-016` both assume, and what remains is
  the index.
- The floor turns a query somebody relies on into an empty answer. The single
  result that clears on the first query is the near case. At 0.60 it survives,
  and a higher floor takes it with the collisions.
- A feedback reports the opposite cost — a two-word query answered as a miss
  because no page carries both of its words. Then the coverage share is the
  wrong measure for a table of contents, whatever it is worth over prose
  sections.
- The miss names the changelog and it does not answer. The entry that carries
  the boundary is in a version the installation does not ship. That is
  `D-ANS-010`'s first **Wrong if** on this server's other corpus.

## Since then

The third **Wrong if** fired before the floor existed, measured over the live
index this lookup builds. The four numbers reproduce: two queries clear the
floor nowhere, one clears it once, and two clear it many times at full coverage.

What was not measured is the rest of the bullet those last two came from. Three
queries reach one page and the floor leaves one. Coverage is the share of the
query's weight a page carries, and that page carries one word of each of the
other two. So a floor that returns them is at most a third, and a floor that
empties the first query is above that.
