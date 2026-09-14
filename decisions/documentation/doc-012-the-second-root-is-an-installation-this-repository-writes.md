---
id: D-DOC-012
title: The second root is an installation this repository writes
date: 2026-08-03
status: open
coveredBy:
  - FixtureTest::everyInstallationBackedToolAnswersFromIt
  - FixtureTest::theWrittenInstallationBootsAndItsConsoleAnswers
  - ToolAnswersTest::everyAnswerOnAPageIsOneItsSchemaAllows
---

# D-DOC-012 — The second root is an installation this repository writes

**`tools:record` takes its second recording from an installation written below
`.fixtures/`, rather than from the made `E-SITE`.**

`D-DOC-006` added the second root so the pages would show what a booted TYPO3
answers, and put it on the one machine that had DDEV. What that costs is a
recording that lands in a commit from wherever it last ran.

## Evidence

- A re-record on 2026-08-03 from a checkout without one undid the `E-SITE`
  recording of 2026-08-02. `typo3_changelog_lookup`, `typo3_extension_describe`
  and `typo3_project_describe` went back to one answer per call, and the only
  thing that noticed was a todo somebody wrote by hand.
- Those three answers were invalid against the schemas their own classes
  declare: the schemas require `removal`, `unlistedFlexForms` and `environment`,
  and none of them had those. That is what a recording nothing checks does. It
  is why a re-record against the checkout alone was what made
  `ToolAnswersTest::everyAnswerOnAPageIsOneItsSchemaAllows` green.
- The fixture answers all nine. Its console resolves through `Typo3Cli::viaPhp`
  and answers `language:domain:search`, `fluid:namespaces`, `configuration:show`
  and `debug:backend:modules`. The real probe boots its `vendor/autoload.php`
  and comes back `full` with every topic, `derivedColumns` included. Eight say
  `answeredBy: "installation"`. `typo3_changelog_lookup` and
  `typo3_project_describe` say `packages`, there as against a checkout, because
  both read files and neither asks the console.
- What the exchange costs, per page: `typo3_schema_lookup` 897 → 279 lines,
  `typo3_backend_module_lookup` 755 → 192, `typo3_icon_lookup` 1,021 → 723,
  `typo3_label_lookup` 747 → 527. A site installation has a real TCA and a real
  module tree behind it, and the fixture has what its writer gave it.
- What it buys is the three pages that had no second answer at all.
  `typo3_changelog_lookup` 765 → 892, `typo3_extension_describe` 870 → 969,
  `typo3_project_describe` 212 → 296.
- The directory goes 13,933 → 12,721 lines, and not all of that is this. The
  same run brought pages current that still called `typo3_hint_lookup` by the
  name it had before `7553cb3`. `typo3_server_scope` still listed a coverage
  without the two tools that read Forge and Gerrit.

## Decided

- The command writes the root rather than finds it. `tools:record` writes it on
  its way past, so nothing has to exist first and every machine records the same
  thing. That is the property the first root has for the same reason.
- Not committed, for the reason `.checkouts/` is not. The class that writes it
  is the source, and a copy of its output beside it is a second thing to keep
  true. It costs no network and no minute, so a second write is free.
- Shaped by `ToolCalls` rather than after a real site. The label the search asks
  for is in it, and so is the tagged deprecation the sweep asks for. The queries
  that ask for nothing find nothing. Shaped after a site instead it would be the
  `E-SITE` again, with a corpus that has nothing to do with the questions it
  gets.
- Every entry in it says whose it is. Issue numbers far outside the range Forge
  has issued, titles that name the fixture, one extension called `acme_events`.
  An answer recorded here is true of this installation and of nothing else. So a
  changelog entry a reader could take for TYPO3's own is the one thing it must
  not carry.
- The heading says which root a reader looks at, and `describeRoot` names the
  fixture first and names it as one. `D-DOC-006` is why that is not decoration.
- `FixtureTest` holds the fixture, which moves the line `D-DOC-006` drew by
  exactly one case. Still nothing checks the recording; something checks the
  installation it now runs against, because this repository produces it.
- Rejected: the `E-SITE` as a third root. Two answers per call is what a reader
  compares. What the third would add is a bigger corpus rather than a state the
  other two do not reach.

## Assumed

- A fixture answer shows the shape well enough to be worth its lines where a
  real one showed the substance too. Nothing measured that, and
  `typo3_schema_lookup` is where it is furthest from the truth: 4 derived
  columns against a real table's dozens.

## Wrong if

- A reader takes a fixture answer for what TYPO3 does. Then the root's name at
  the heading is not enough and the answer has to say it at the block.
- A state an installation-backed tool meets in the wild is one the fixture never
  produces. It answers what its writer built it to answer, so the states are the
  ones somebody thought of. That is exactly what a real installation is not.
- The `E-SITE` catches something again the way it caught the join `ddev exec`
  makes of its arguments into a line (`D-DOC-006`). The fixture reaches its
  console through `Typo3Cli::viaPhp` and never through DDEV. So nothing here
  exercises that transport at all, and the one fault this surface has ever found
  was in it.
