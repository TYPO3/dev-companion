---
id: D-CAT-008
title: A component entry's classes carry a derived position and range
date: 2026-08-24
status: open
coveredBy:
  - BackendCssTest
  - CatalogTest::aClassIsAnsweredOnAMajorItsEntrysListDoesNotReach
  - CatalogTest::aWrapperIsNotListedAmongTheModifiersOfWhatItWraps
  - CatalogTest::noClassIsListedByTwoEntries
  - CatalogTest::whatIsStyledWithinAClassIsNotWhatItRequires
  - StyleguideListingTest::aCheckoutShippingNoStyleguideAnswersNothing
  - StyleguideListingTest::aComponentTheStyleguideDoesNotListIsNotListed
  - StyleguideListingTest::aControllerWithoutTheListAnswersNothing
  - StyleguideListingTest::anAssignedVariantIsNotAComponent
  - StyleguideListingTest::theListedComponentsAreWhatTheControllerOffers
  - StyleguideListingTest::theOverviewIsNotAComponent
---

# D-CAT-008 — A component entry's classes carry a derived position and range

**Each class in a component entry carries where it sits and the majors it holds
on. Both come from the compiled `backend.css` rather than from a hand.**

A review on 2026-08-24 found what the curated lists cost. A caller told that
`table-fit` exists still attaches it to the wrong element. The entry keeps its
structure in one markup string and its class names in flat lists beside it.

## Evidence

Measured on 2026-08-24 against `.checkouts/` 12.4, 13.4, 14.3 and main.

- The curated entries place 69 of 243 class names, where placed means the
  entry's own markup shows the name. `table-fit` sits under `modifiers` beside
  `table-striped` and `card-container` under `subComponents`. The entries' own
  markup puts both of them *above* the component.
- `backend.css` sits committed on every branch and carries 3975 selectors on
  14.3. It states what the lists lost: `.table-fit>.table`,
  `.card-container .card`. 235 of the 243 names appear in it.
- The classes a caller can misapply are the ones it places. On 14.3 it makes 8
  of them a wrapper around the root class and 12 a descendant of it. The 122 it
  leaves free are modifiers that carry no position to get wrong.
- **A position can bind to a version.** `.dropdown-menu` stands above
  `.dropdown` from 14 and not before, and `.panel-heading` shares an element
  with `.panel` from 13. Both come off the selectors and neither is in the
  entry. A first read of this said `table-fit` was one of them, off a truncated
  listing. It stands above `.table` on all four.
- **The derivation places 40 of the 242 curated names and leaves 203 unplaced.**
  A position exists only where the core happened to write a selector that
  carries one, and it can go. `.card-header` sits below `.card` on 12.4 and on
  no later major, because the CSS flattened while the class stayed nested. So
  this answers the wrapper cases it exists for and is not a structural map.
  Partial and true still beats the lists, which call `table-fit` a modifier of
  `.table` and are wrong rather than silent.
- A range per class falls out of the same read. 168 names hold on all four
  majors and 70 on fewer. 17 of 26 entries carry classes whose ranges differ, so
  one `classesSince` per entry is too coarse for two thirds of them.
- `D-CAT-006` rejected a range per class because 120 pairs would need a
  derivation by hand on every core release. Derived from a committed file, they
  do not, and the read of the four branches is the verification rather than a
  step after it.
- `@customElement` is the core's other machine-readable surface: 59 declarations
  on 12.4, 90 on 13.4, 132 on 14.3, none of them in the catalog. Nobody can
  attach an element to the wrong node, which is the whole of what went wrong
  here.
- The core states none of the rest. It ships no component manifest and no
  schema. So nobody answers what a component requires, what is public API and
  what may nest in what.

## Decided

- `bin/cli components:derive` derives the classes and the elements, per covered
  major, and writes them to files of their own. So nothing hand-edited sits in
  what a command overwrites. `catalog:check` verifies the committed result
  against the checkouts, as it does for everything else.
- Each class carries where it sits relative to its component's root, around it,
  on it, or below it, and the range that position holds on. A position says
  nothing about whether the component requires the class, because a selector
  cannot.
- What the core styles inside a class is a separate field and says so in its
  name. `.table-fit>typo3-backend-progress-bar` is inventory: styled if it is
  there, required by nothing.
- Each class carries its own range, and the entry's bound uses it. An entry
  withheld whole for one late custom property can answer the classes that were
  there all along. That is what `D-CAT-006` reached for and could not give.
- The curation stays, and stops to hold facts. What a person decides is which
  components are worth an answer, their titles, their summaries and the one
  worked example. What a machine decides is every fact about a class.
- **`variants`, `modifiers` and `subComponents` stop to mean a position.** The
  three names claim one, and for 203 classes nothing verified it. `table-fit`
  sat in `modifiers` and `card-container` in `subComponents` while both are the
  element above. They are the component's class names, and where a class sits is
  the derived field or nothing.
- A document says how the styleguide works. What it is, and that it is a system
  extension from 13.4 and an installable package before that. Where its demos
  live, and that its examples are complete and cannot shrink.
- Rejected: the derived table alone in place of the curated components. The
  table holds 3307 classes against 241 chosen by hand, and nothing else keeps a
  backend-internal class out of an answer. What that curation is still worth is
  readable once the derivation has the facts, and a guess before.
- Rejected: a render of the styleguide as a source. It would need an
  installation per major, a format to adapt and a verification run on each. A
  committed file in the checkout answers all four at once.

## Assumed

- That a selector is evidence of intent. `.table-fit>.table` proves the core
  styles that combination, which is strong and is not a promise, and the answer
  says so where it answers.
- That the difference between the two sources brackets what neither states. What
  the derivation places is scaffold and what only the styleguide shows is
  decoration. The inference is the best available and it is an inference.
- That `backend.css` stays committed. It is a build artefact in the core's own
  repository, and a release that stopped to commit it would take the source
  away.
- That the curated selection stands in for a statement this entry does not make.
  `D-CAT-004` selects on what the core files as a component, a Sass partial, a
  custom element. Not everything it admits is for an extension to use. What
  settles that is the styleguide rather than the selection, and
  `todo/open/2026-08-24-014500` carries it.

## Wrong if

- A derived position is wrong on a class somebody borrows, which a rendered
  styleguide would show and the selectors did not.
- The derivation needs hand correction on more than a few classes per release.
  Then it is curation with extra steps, and the lists were the honest form of
  it.
- A class-shaped question still gets a name and a range as its answer. That is
  what `D-CAT-006` left, and the position is what this adds. If the answer does
  not carry it, nothing here reached the caller.
- The 203 unplaced classes are what callers ask about, and the answer is silent
  for them where the lists at least guessed. What would show it is a feedback
  that names a class the derivation left unplaced.
- The core ships a component manifest. This entry exists to go out then, and the
  position field is what would stay.
