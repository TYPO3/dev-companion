---
id: R-SKL-005
title: 'The order a task starts in is written once'
status: held
restsOn: [D-EVI-003, D-SKL-003, D-SKL-004, D-SKL-034, D-SKL-037]
heldBy:
  - InstallerTest::codexInstallAndUpdateTrackTheirSkillsCentrally
  - SkillTest::anAssessmentAsksBeforeItJudgesAndSaysWhatItDidNotAsk
  - SkillTest::everySkillStartsFromTheBaseBeforeItsOwnEvidence
  - SkillTest::theBaseFixesTheOrderEveryTaskStartsIn
  - SkillTest::theChangelogsSilenceIsNotAnAnswerAboutWhatStillWorks
  - SkillTest::theDeprecationSweepIsSkippedWhereNoTypo3ApiIsTouched
  - SkillTest::theDeprecationSweepRunsFromTheExtensionsSurface
  - SkillTest::theFilesAChangeWillTouchAreNamedBeforeTheFirstEdit
  - SkillTest::theInstalledSourceIsTheStepAfterTheLookups
  - SkillTest::theOrderSaysWhatItEstablishedBeforeTheReading
  - SkillTest::theReportNamesTheStepsOfTheOrderItDidNotReach
  - SkillTest::theWorkflowStepRunsInEverySession
---

# R-SKL-005 — The order a task starts in is written once

**One page states the order a task starts in, and every published skill carries
it.**

It is the installation and its commands, the extension and what it ships, and
the workflow. Then the conventions of each subsystem in scope, and the
deprecations of the installed core over what that extension ships. Only then the
checkout.

One of those steps carries a condition, and it names what makes the step empty
rather than optional. The session skips the deprecation sweep where the change
touches no TYPO3 API. A deprecation is a statement about API the package calls,
and a change that calls none leaves the sweep empty before it runs. Which side a
change falls on comes from the files it touches rather than the task it started
as. Everywhere else the session runs the step. A prescription that gets skipped
teaches the next reader to skip the ones that matter too.

A task that produces no change does not reach that step at all. The property is
what the task produces; a triage, a reproduction and a review illustrate it and
are not the list it comes from. The exemption ends where the workflow produces a
change. A review asked to make the change is that other workflow. It starts the
order again with the files it is about to write in hand.

The order closes on the report, which names every step of it the session did not
reach and what stood in for that step. That obligation stands after the order
rather than inside each step it covers. A session that takes an exemption reads
the paragraph that grants it least carefully. This is the one instruction of it
that has to survive into a document written later. A step passed over in silence
looks the same as one the session dropped.

The workflow step carries none. The session runs it in every session, this
skill's own tasks included. The brief comes from the caller's paths as well as
the task text, and no skill knows those paths. So a skill that covers the task
is not that brief, and a skipped step costs the hints and core checks the paths
match. Where the guide's own answer named the skill, that costs one call for an
answer already in the session. That is the price of a step there is nothing to
decide about.

A skill states what it adds to that order, never a second copy of the order
itself. The base also separates the two kinds of lookup, so a session does not
take a runtime answer for a verdict. It says a session reads a returned rule
against the code that exists as well as the code it is about to write, in both
directions. A mechanism that costs something is not a defect for that cost. So
the session establishes what it is there for from the repository's own
statements first. A documented purpose makes it a trade-off to name with its
cost rather than a finding. Where the session can establish no purpose, the
finding says that instead of a conclusion that there is none.

The deprecation sweep is part of that order rather than a step a finding
triggers. The changelog's own axes bound it: the type, each major the package
declares, and the index tag, with no query at all. What the extension ships
picks the tags rather than the words. That is the system extensions it requires,
renders through or registers into, and the surfaces its files are. That is why
the sweep still exists before the session opens a file. The session verifies
each identifier it returns in the checkout. The `FullyScanned` /
`PartiallyScanned` tag reaches the answer because it says whether the Extension
Scanner finds the remaining call sites or the reader does.

That step also says what an empty result is worth. A changelog records change
events, and a pattern nothing changed has no entry. "Does this still work in
version N" goes to `typo3_documentation_lookup` at that version, there and
whenever the read raises it again. What that answers is a documented surface,
because the manual matches page titles and section paths and never the text of a
page. A PHP identifier has no page with its title, and goes to
`typo3_changelog_lookup` under its own name and then to the class. Where the
manual has no page for a surface either, that miss is a result rather than an
answer.

A behaviour question that survives all of them goes to the installed source: the
class that implements it and the one it inherits from. That is the step after
the lookups rather than in place of them. What it replaces is a change to the
code until it works. What it settles is what this installation does and never
what TYPO3 supports. So a finding says the question stays unsettled beyond the
installed version, and an answer built on the read names the version it holds
for. The base names both dispositions, because a session that has to produce
working markup cannot write a finding.

It also names the three things a finding can rest on. That is a file read at its
path and line, a command that ran, or a mechanism traced into an installed
package. It requires the finding to say which of them it is, because otherwise a
derived finding gets the weight of an established one. And it sends the session
to the second of the three where the repository already declares it. Even a task
told not to change files runs the commands `typo3_project_describe` marks as
checks. It does not run the ones it marks as changes. It names an unknown as
evidence that is available rather than runs it unasked.

## From

Three `REVIEW-01` runs (2026-07-31) and the divergence they exposed. A session
repaired the conformance skill while the content-element, documentation and test
skills still put the read of the checkout ahead of the conventions lookup. That
is the arrangement those runs measured. `REVIEW-02` (2026-07-31) extended it.
That run reported five of six priorities against mechanisms the package ships on
purpose. Those were a compile step a setting drives, and a vendored copy that
makes a non-Composer install work. One was a font download that keeps the file
on the site's own host. Three recorded `REVIEW-02` runs in two repositories
(2026-07-31) extended it again. They executed no project-owned command of the
ten and five on offer, and said so nowhere in their answers. `D-EVI-003` decided
separately and afterwards what those runs owed: two of the fifteen were checks,
and a session runs a check. The `REVIEW-02` run in an extension that declares
two majors against an installation a major behind (2026-07-31) extended it once
more. It called `typo3_changelog_lookup` four times and never once with
`type: deprecation`. It reported the frontend surface as free of superglobal
access with 24 call sites in 11 files against a controller the installed core
marks deprecated. It named the one deprecated API it found because a ViewHelper
finding walked it there. The bootstrap_package conformance review (2026-07-31)
extended it a last time. It ended two findings in "I had to read installed
vendor core". Both asked the changelog whether a pattern still worked in 14 and
read its silence as the answer. `typo3_documentation_lookup` at that version
answered one of them in a single call (`D-ANS-010`, re-run 2026-08-02). The
sweep's own bound came last. Two models swept one sitepackage on the same day
with word queries the step told them to derive. Neither got anything back
(`feedback/2026-07-31-194459`, `feedback/2026-07-31-194819`). `D-SKL-003`
carries the re-run and what the two bounds return. The step after the lookups
came from the other kind of session. `feedback/2026-08-01-003933` built a
content element in `site-new`, guessed at the `f:if` branch contract and changed
the markup until the user corrected it. The base's one sentence for an exhausted
question addressed a review it was not in (`D-SKL-004`). The two conditions came
last, from one session that added a code style fixer to an extension in
`/home/benji/projects/ext-guidedtour` (2026-08-04). Routed to
`typo3-extension-testing`, it skipped steps 3 and 5 and reported both.
`feedback/2026-08-04-055741` warns that a prescription which gets skipped
teaches the next reader to skip the ones that matter too.
`feedback/2026-08-04-055715` asks what the guide adds when a skill has already
routed the task. Which of the two readings to write is a question about what the
maintainer wants. The maintainer answered it on 2026-08-04: the narrow one, in
`D-SKL-015`. The workflow step's half of it came off again on 2026-08-11. Two
sessions the condition did not cover skipped the step anyway and neither said so
(`D-SKL-034`). The sweep's other exemption became a property after
`feedback/2026-08-11-055337`. A review of one Gerrit change read the three
examples as the list its own shape was not in. It skipped the sweep on a diff
that touches TYPO3 API, and said so nowhere in its report (`D-SKL-037`).
