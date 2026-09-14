---
id: D-SKL-031
title: 'A triage picks a candidate on where the symptom shows'
date: 2026-08-09
status: open
---

# D-SKL-031 — A triage picks a candidate on where the symptom shows

**A triage picks a candidate on where the symptom becomes visible and on how
much of the constellation the suite already models.** The triage skill says so
where it hands the backlog over.

[`D-ANS-069`](../answers/ans-069-a-backlog-row-carries-the-review-server-and-not-the-journal.md)
widened the row and left the criterion open. A read of a row is this
repository's question and what makes core work cheap is not.

## Evidence

- The reporting session's own set, re-read on 2026-08-09. The issue it settled,
  #58705, is a TypoScript reproduction. It stands `Under Review` with change
  95182, pushed on 2026-08-08. `.checkouts/main` already carries
  `frontend/Tests/Functional/Imaging/GifBuilderTest.php` and
  `frontend/Tests/Functional/ContentObject/FilesContentObjectTest.php` over the
  two objects the report names.
- The four it decided cheaply divide by where the symptom is. Issue #82228
  carries an abandoned change on the review server. #83913 and #81102 are
  backend interactions, and no `Tests` directory in `.checkouts/main` names the
  update signal #81102 is about. #83848 carries no reproduction at all.
- A test over the class is not the signal on its own. Issue #85456 has
  `backend/Tests/Unit/Form/FormDataProvider/TcaColumnsOverridesTest.php`, and
  all three providers it names exist on `main`. It is still not settleable. The
  mechanism is the order between them, which a test of one provider alone cannot
  see. The reading is the level the symptom appears at, not the file.
- The category reading is free and rare. Of the 55 categories the core project
  files under, `t3editor`, `RTE (rtehtmlarea + ckeditor)` and
  `Language Manager (backend)` name subsystems `.checkouts/main` no longer
  ships. Every category on the session's own page of stale Bugs names one it
  still does.
- "Browser-only" is not that no layer can hold it. Every covered branch has a
  browser layer: Codeception on 12.4, both on 13.4, Playwright alone on `main`.
  Each needs an installed instance and a browser before a session sees anything,
  which is the cost rather than the absence.
- What the suite models is wider than a category suggests.
  `core/Tests/Functional/DataScenarios/` carries the flex, group, many-to-many
  and category constellations and a workspaces variant of each. So a relation or
  workspaces report is not expensive by its subject.

## Decided

- The skill states five readings under "Find the candidates", cheapest first.
  The first two are what already happened to the issue and the category against
  the branch. The other three are where the symptom appears, how far the
  mechanism reaches, and what the suite already models.
- The skill states them as what to read rather than as field names. The row that
  carries the first of them is a parallel change, and prose survives whatever
  that change calls its keys.
- The skill says nothing about the tracker's own difficulty fields.
  [`D-ANS-069`](../answers/ans-069-a-backlog-row-carries-the-review-server-and-not-the-journal.md)
  measured them empty on exactly the backlog where the question arises. A skill
  that names a field nobody fills spends a session's read on it.
- The section ends with two asks. The answer names which reading decided, and
  says of the rows it passed over that it passed them over. A skip is not a
  triage, and the escape hatch above it already owes a "why".

## Assumed

- One labelled set. Nine issues, one reporter, one page of stale Bugs, and
  nobody has read whether the other twenty-one are settleable yet.
- The fifth reading rests on what `.checkouts/main` models today. A suite
  reorganisation moves it, and the skill says to look rather than what to find.
- The settled issue's patch was never read. Change 95182 answers `Not found` on
  the review server's own interface, bare and project-qualified. So this entry
  read what made #58705 cheap off the report and the checkout rather than off
  its diff.

## Wrong if

- A session picks by these five and still spends the session on an installation
  setup. That would say the session answers the third reading off the subject
  rather than off the report.
- The category reading never fires again over a real backlog, which would make a
  free reading a paid one.
- A candidate rejected as an interaction turns out to be one class after all.
  That would say the fourth reading trusts a reporter who guessed.

## Since then

The five readings are in the published skill in the order this settled. Their
heading reads "Where you do pick, pick on where the symptom is visible and on
how much the checkout already models it". The section closes as **Decided**
asked: "Say which of those decided, and say of the rows you passed over that you
passed over them."

Nothing has run against it. `scenarios/runs/` holds three recorded forward runs
and none is a triage. No feedback since 2026-08-09 comes from a triage session.
The three that name triage at all are patch reviews that quote the neighbouring
skill. So nothing has disturbed the three **Wrong if**, and nothing has held
them. Each needs one session that works a real backlog. What it would report is
which reading decided, which is the sentence the skill already asks for.
