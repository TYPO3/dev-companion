---
id: D-KNW-032
title: The corpus is filed by question, and two splits were taken back
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aNewLabelNamesTheSourceLanguageAndWhereItsTranslationGoes
  - HintsTest::everyHintIsReachedByItsOwnTitle
  - HintsTest::settingTestsUpInAPackageReachesTheHintAboutThat
  - HintsTest::upgradingAnInstallationIsAnsweredAsAnOrderOfOperations
---

# D-KNW-032 — The corpus is filed by question, and two splits were taken back

**Every hint that held more than one question splits along `D-KNW-030`'s axis. A
split that dropped a rule a task has to reach merges back.**

The corpus went from 66 hints to 120 with no statement gone. The mean body falls
from 297 words to 174, and the headroom under the dilution ceiling from 3 words
to 126.

## Evidence

- Eighteen umbrella hints split, largest first. `extension-files` at 1076 words
  into five, `project-extension-tests` into four, `sitepackage-initial-content`
  into four, `content-elements` into four. `sitepackage-layout` into four,
  `fluid-templates` into four, `site-sets` into four, `frontend-page-rendering`
  into five. Plus `extbase`, `project-repository-layout`, `language-files`,
  `environment-variables`, `browser-tests`, `installation-upgrade`,
  `frontend-records`, `extension-repository-layout` and
  `dependency-injection-services`.
- Eight of the new hints were out of their own title's reach, which
  `hints:coverage` reports and `HintsTest` fails on. It was the domain gate
  every time and never the score. "Conditions, Escaping and Array Literals"
  carries no Fluid signal, so the query fell back to PHP and the hint's own
  domain was never a candidate. Each title gained the word that detects its
  domain.
- The `any` share did not move. `D-KNW-029` carries that reading.

## Decided

- An entry hint keeps the id the old umbrella had wherever something outside the
  corpus names it. `project-extension-tests` is in `task-intents.json` and in
  two tool descriptions, `extbase` and `sitepackage-layout` in requirements. The
  split hangs off that id rather than replaces it.
- The entry hint names its neighbours in a last statement. A caller who lands in
  the middle of a family otherwise cannot see that the rest exists.
- `language-source-locale` and `upgrade-commands` merged back into their entry
  hint. Both had a rule that has to travel with the general question. The
  source-language correction reaches a task that never mentions a locale, and
  the order of operations is what "how do I upgrade" asks for. A split that
  costs a stated requirement is a wrong split, whatever it does to the mean.
- A pattern joins a hint only where it discriminates within its family. "add
  tests for" on `project-extension-tests` put the project hint into a core
  DataHandler question. That is the same failure the bare `DataHandler` pattern
  caused in `D-KNW-030`.

## Assumed

- The 39 hints still over the dilution reference are single subjects that happen
  to be long. The longest is 461 words and each had a read during its split. The
  next one to read again is whichever a scenario reaches for the wrong question.
- Naming the neighbours in prose is enough for a caller to find the family. The
  alternative is a declared relation between hints, which is a field nobody has
  needed yet.

## Wrong if

- A caller gets the entry hint and never the specific one, because the entry now
  carries both the general vocabulary and a pointer. That is one hint with two
  jobs again, and it would show as an entry that outranks its own family.
- A split family answers the same question three times over. The family crowds a
  limit of four or six, which is what the vocabulary rule above exists to
  prevent.
- Another split turns out to have dropped a rule the way the two merged ones
  did. That would mean the axis applies with no read of what reaches a
  statement.

## Since then

The second assumption is about the search for the family. The first report to
separate the search from the take is a session that read the last statements. It
saw the ids and followed about half of them. So the names worked and the branch
fell anyway, at the cost of five review results and an HTTP 500.

That is not this entry's **Wrong if**. It narrows the assumption instead.
Neighbours named in prose are enough to find the family, and that says nothing
about a caller who has just got what it came for.
