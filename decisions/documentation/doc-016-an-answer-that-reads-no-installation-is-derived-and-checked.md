---
id: D-DOC-016
title: 'An answer that reads no installation is derived and checked'
date: 2026-08-04
status: open
coveredBy:
  - CoreFixtureTest::everyAnswerThatDoesNotMoveWithARootIsDerivedFromOne
  - CoreFixtureTest::theWrittenCheckoutIsReadAsOneAndSaysWhichTypo3ItIs
  - ToolSurfaceTest::everyPageIsWhatTheServerDeclares
---

# D-DOC-016 — An answer that reads no installation is derived and checked

**A tool whose answers read nothing an installation contains has its
`## Answered` half derived by `bin/cli tools:index` and held by
`bin/cli tools:check`. The rest stay recorded.** The derivation runs against a
core checkout this repository writes, which declares its identity and holds no
content.

`D-DOC-006` made the whole of that half evidence because a filled answer needs
an installation. For part of the surface it does not, and what the recording
held there was a derivation nobody checked.

## Evidence

- Measured on 2026-08-04 over the 43 calls `ToolCalls` drives that reach no
  host. Two roots answered: `.checkouts/14.3`, and a root with a `composer.json`
  that declares `"type": "typo3-cms-core"` and one `Typo3Version` class. Eight
  tools came back byte-identical over their 20 calls and every other tool moved.
- The first pass compared against no installation at all, and only
  `typo3_commit_message_guide` held. Every difference was one of two values:
  `scope`, from `Instance::startedIn()`, and `targetVersion`, from the version
  read off the installation. Both are the root's identity rather than its
  content. That is what makes a root that declares only those two the right
  comparison and not a weaker one.
- `typo3_translation_domain_lookup` is the near miss. It is identical from a
  root that declares `14.3.6-dev` and different from one that declares `14.3.0`.
  It prints the installation's exact version into its text. So a derived page
  would state a patch level nothing here has.
- A regeneration of the eight pages moved six of them in the first sentence
  alone. `typo3_hint_lookup` and `typo3_task_guide` moved further, and both ran
  from the same two roots afterwards and came back identical. Their recorded
  answers were from 2026-08-03 and `knowledge/` had moved since. That is
  `D-DOC-006`'s second **Wrong if** in the corpus rather than as a worry.
- What the option not taken costs, read off the pages as they stand.
  `changelog: hit` answers `"ext_tables.php in extensions"` from the checkout
  and `"ext_tables.php in the fixture extension"` from a fixture. `extension`
  answers `"TYPO3 CMS Backend"` against
  `"The fixture installation's backend package."`

## Decided

- **The line runs between pages, never through one.** A page is wholly derived
  or wholly recorded, and its first sentence says which. That is what
  `D-DOC-007` drew between the two halves of a page, kept. A section split into
  a checked part and an unchecked one is the shape both entries exist to
  prevent.
- **The set comes from a measure, not a declaration.** `ToolCalls::derived()`
  names it and `CoreFixtureTest` holds it from both ends. A tool in it whose
  answers move is a checked page that asserts one root's content. A tool outside
  it whose answers do not move is a recording nobody needs.
- **`CoreFixture` holds nothing but the identity** — three files, no packages,
  no console, no changelog. Content in it would reach a derived answer. A page
  that claims a check behind it would then show this repository's fixture where
  a reader expects TYPO3.
- Rejected: the fixture as `tools:record`'s primary root, which is the answer
  this card went back with on 2026-08-04. It buys the same check for the whole
  surface and pays with every real answer on it. The two calls above are what
  that reads like, and `D-DOC-012`'s first **Wrong if** is the entry that
  already names the cost. Put back to the maintainer with the measurement, the
  answer was to check what is derivable and keep the rest recorded.
- Rejected: the set read off `answersFrom()`. `typo3_test_run_guide` and
  `typo3_script_lookup` declare `knowledge` alone and still moved with the root,
  because `Scope` asks the installation which repository the session is in. A
  declaration about where an answer comes from is not a statement about what
  moves it.
- The tools that reach a host stay recorded and `CoreFixtureTest` does not drive
  them. `D-DOC-008` is why they are in the table at all. A drive of one here
  would put this suite's requests on somebody else's service twice per root.
- A tool that answers from the installation is outside the question rather than
  outside the set. Neither test root has a console, so the two agree for a
  reason that says nothing about a caller who has one.

## Assumed

- That kind and major exhaust what a caller's root contributes to these eight
  answers. The measure ran on one day against one checkout, and the variant root
  in the test is what measures it again.
- That a reader takes a derived page as the answer a caller gets rather than as
  an example. Nothing measures that, and it is the same assumption `D-DOC-006`
  makes about a recorded one.

## Wrong if

- A derived tool starts to read the installation and the test stays green,
  because the variant root has nothing of the kind it reads. It carries a
  package, a stylesheet and a changelog for exactly that reason. A new kind of
  read needs a new thing in it before the measure means anything again.
- A caller on another major takes a derived page for their answer. Each one
  states the version of its derivation in its first sentence and nothing else
  says it.
- `bin/cli tools:check` grows into what `tools:record` is. It writes a fixture
  and makes 20 calls today. None of them reaches a host, boots a container or
  needs `.checkouts/`, which are the properties that let it run in CI at all.
