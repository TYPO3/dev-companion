---
id: D-SKL-108
title: 'A review owes the breaking changes of a declared major above the installed one'
date: 2026-10-28
status: open
coveredBy:
  - VersionsTest::aReviewOwesTheBreakingChangesOfADeclaredMajorAboveTheInstalledOne
---

# D-SKL-108 — A review owes the breaking changes of a declared major above the installed one

**A review of a package that declares a major above the installed one owes
`typo3_changelog_lookup` once per such major, with `type: breaking`, no query.
`typo3_task_guide` says so in a review brief, in the text and in `owed`.**

`typo3-extension-health` points at that line beside the declared-major
procedures, without the tool name, because `SkillTest` keeps the sweep's call in
the base alone. The sweep's exemption in `skills/base.md` stays as it is.

## Evidence

- **The miss.** The third `REVIEW-02` run of 2026-10-28
  (`scenarios/runs/REVIEW-02.json`, server `3482d798`) made no
  `typo3_changelog_lookup` call. Its **Not reached** took the base's exemption
  for the sweep. The package declares `^14.3 || 15.*.*@dev` and the installation
  runs 14.3.7.
- **The find.** The second run of the same day asked `typo3_changelog_lookup`
  with the query `AfterFileProcessingEvent`. It found Breaking-110702 against
  `Classes/Service/ImageInfoService.php`, its first finding.
- **What each shape costs, measured on 2026-10-28 against
  `/home/benji/projects/bootstrap_package` at `244d65a9`.** `Classes/` imports
  61 distinct `TYPO3\CMS\` names, so one query per identifier is 61 calls.
  `version: "15"`, `type: "breaking"`, `tags: ["PHP-API"]` returned 33 entries
  in one call, and "Widened FAL processing task interfaces" (110702) is among
  them. Without the tag the same call returned 41 entries, 110702 among them.
  The query `ProcessedFile` at version 15 returned that entry alone.
- **The exemption is about something else.** Step 5 of `skills/base.md` says the
  sweep asks what a package will have to stop calling. A breaking entry on a
  declared major is a defect today, whatever the task writes.
- **The guide is what a session acts on.** `D-ANS-191` and `D-EVI-011`: Claude
  Code hands the model the data half. The same run followed `surfacesOwed`,
  which names a call, and dropped the skill text that asks for none.

## Decided

- **One call per major, not one per identifier.** The major's breaking entries
  are a list the session reads against the code. An identifier query is the
  follow-up for an entry the session doubts.
- **`breaking`, not `deprecation`.** A deprecation on the newer major still
  works there.
- **Only a major above the installed one.** The installed core already shows
  what broke up to its own version. A major below has the other question, an API
  the code calls that the older line lacks. A breaking entry does not answer
  that, and `a-declared-major-that-is-not-installed` does.
- **In the review brief, not in the base.** The base goes into every skill, and
  only a review takes the exemption. A change already owes the sweep, and the
  brief for a change carries it.

## Assumed

- That a session holds a list of breaking entries against the code without a
  query per identifier. The second run found 110702 by an identifier, not by a
  list.

## Wrong if

- A review gets the line, makes the call and still misses a breaking entry the
  code calls. Then the list was the wrong shape, and the identifier query is
  what the line must ask for.
- A run reads the files an entry names and still clears an entry the code calls.
  Then the read needs the newer major's signature beside it.
- A run makes the call and the answer is too long to read against the code. Then
  the line must name `tags`.
- A review of a package that declares a major below the installed one misses an
  API that line lacks. Then that direction needs a line of its own.

## Since then

- **The first Wrong if fired on 2026-10-28.** The fourth `REVIEW-02` run made
  the call, got 41 entries, held 10 and missed Breaking-110702. The repair is
  not the identifier query this entry named, which is 61 calls for that package.
  The line now asks for `paths`, and the answer keeps the entries naming a class
  the code uses — `D-ANS-192`.
- **The fifth `REVIEW-02` run of 2026-10-28 got 110702 and cleared it.** The
  entry named `Classes/Service/ImageInfoService.php`, and the session cleared it
  with a PHPStan run against the installed 14.3.7. That analysis reads the
  installed core, so it cannot show a v15 signature. The line and the skill now
  say that such a check clears no entry: the session reads the file, or the
  entry stands under **Open**.
