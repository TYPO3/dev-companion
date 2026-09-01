---
id: D-KNW-108
title: 'Where an impexp import puts the records it writes is a subject this server owns'
date: 2026-08-24
status: open
readings:
  - 2026-09-01
---

# D-KNW-108 — Where an impexp import puts the records it writes is a subject this server owns

**What decides the page an imported record lands on is inside this server's
boundary and missing from it, so the feedback is queued at `normal`.**

The corpus is not silent on impexp. It answers a query about the import route
with the hint about writing the export, which is the neighbour the reporting
session read and put down again.

## Evidence

- Re-run on 2026-08-24 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own task reaches `datahandler-basics`,
  `datahandler-testing` and `language-files`, and no hint that names impexp at
  all.
- The queries that do reach impexp reach the export half.
  `"impexp import writes a page tree back"` ranks `impexp-artifact` first, then
  `datahandler-seeding` and `initial-content-import-once`; the bare path
  `"typo3/sysext/impexp/Classes/Import.php"` ranks `system-extension-boundaries`
  first and `impexp-artifact` second.
- What the corpus holds about impexp is three statements, and none of them says
  what decides a record's pid. `impexp-artifact` is the export options an
  artifact needs, `initial-content-import-once` is the registry row that stops a
  second import, and `initial-content-references` is which relations survive one
  and what `Import::processSiteConfigurations()` rewrites.
- Everything the feedback claims about the write path holds, read in
  `.checkouts/main` at `3cbdea24dd`. `Export.php:192` writes each page
  translation into `header.pagetree` under its own uid, with a comment saying
  the uid index is added so the record counts as `insidePageTree`; the node
  carries a `uid` and no `subrow`.
- `ImportExport::flatInversePageTree()` at line 1013 reverses every level with
  `array_reverse()` and passes `-1` as the pid of the top level, so the appended
  translation nodes come out of it first and carry `-1`.
- `Import::writePages()` at line 807 takes each page's pid from
  `header.records.pages[uid].pid`, maps it through `importNewIdPids` and falls
  back to `$this->pid` where the lookup misses. `addSingle()` fills that map at
  line 1079 as each page is added, so a page reached before its parent lands on
  the import target rather than in the tree.
- `addSingle()` unsets `pid` where the record maps to an existing uid and sets
  it only for a `NEW` id, at lines 1096 to 1108. That is why an update cannot
  move a page through the datamap, and why `writePagesOrder()` exists.
- `writePagesOrder()` at line 859 returns unless `$this->update`, and its move
  is guarded by `$pagePid >= 0`, so the nodes carrying `-1` are outside the
  correction pass as well.
- The statement is unbound. The same five mechanisms stand on `.checkouts/12.4`
  at `31f881a212` (`Export.php:273`, `Import.php:727`, `781`, `793`, `1006`), on
  `.checkouts/13.4` at `50cbcb5bef` (`Export.php:198`, `Import.php:741`, `793`,
  `805`) and on `.checkouts/14.3` at `fdef40e2f8`, whose lines are main's.
- The feedback's own line numbers are one or two off these — `1084` for the
  `importNewIdPids` assignment and `907` for the guard — because it read the
  installation it was working in. The methods are what the hint can name.
- The adjacent half is a tool that was not called rather than an answer that is
  missing. `typo3_schema_lookup` answers which field carries a translation
  parent from the installation's own TCA, and the corpus already carries
  `l18n_parent` for `tt_content` in `page-rendering.json`. A session reporting
  the tools it loaded and never called is its own subject, and the corpus
  carries that shape from another checkout as `feedback/2026-08-24-140421`.
- `--update-records` is in no file below `knowledge/` or `skills/`, and it is
  the option that switches on the `addSingle()` rule above. It belongs in the
  same statement rather than in a card of its own.

## Decided

- Step 1a, and queued rather than closed on the spot. What lands is a statement
  about TYPO3 read across four checkouts, which
  [`judging.rst`](../../documentation/records/judging.rst) puts on the other
  side of the line from a wording fix.
- `normal` rather than the `low` the card arrived at. A query on the import
  route reaches the hint about the export, and the symptom the placement rule
  explains — records arriving on the import target instead of in the tree — is
  the one `initial-content-references` already serves from the reference side.
- Not `high`. One session reported it, the task behind it was a core bug report,
  and nothing is blocked on the sentence.
- A hint of its own rather than more bullets on `impexp-artifact`. That hint
  answers how the artifact is written; this one answers why the imported records
  are where they are, which is `D-KNW-030`'s split by the question a caller
  arrives with.
- The hint states the mechanism and the class it lives in, not a walk through
  `Import.php` — `D-KNW-062`. The scope keeps PHP source as code on the caller's
  side, and a behaviour named by its method is what the corpus already writes:
  `initial-content-references` names `processSiteConfigurations()` for what it
  overwrites.
- Neither archived nor trimmed. No part of the observation is answered below
  `knowledge/` or `skills/` today, and the adjacent half takes no card here.

## Assumed

- That the symptom the session was verifying — page translations arriving on the
  import target — follows from these five mechanisms. Nothing was imported here;
  what was read is the code, not a run.
- That the double use of `header.pagetree` stays. No changelog was searched for
  a rewrite of it, and the export comment reads as the reason it was built that
  way.
- That one session wrote this feedback and the ones beside it. They share a
  directory, a model, a subject and half an hour, and nothing in a feedback
  records a session.

## Wrong if

- The import gains an ordering pass, so that a page no longer depends on when
  its parent was added. The statement would then hold on the majors below that
  release and the binding would be the sentence worth writing.
- The core fixes the defect the session was verifying by ending the double use
  of `header.pagetree`. The statement would then describe the LTS lines and not
  `main`, which is a binding rather than a deletion.
- A session is handed the written statement and still reads `Import.php` to find
  where its records went. What was missing would have been the shape rather than
  the knowledge, and a hint would have answered a rung it does not reach.
- The statement turns out to be reachable only from a core-contribution query.
  It would then belong to the core scope rather than beside the distribution
  hints, and where it was filed is what this entry got wrong.
