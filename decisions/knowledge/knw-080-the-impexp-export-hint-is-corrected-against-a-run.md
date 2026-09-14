---
id: D-KNW-080
title: 'The impexp export hint is corrected against a run'
date: 2026-08-17
status: confirmed
---

# D-KNW-080 — The impexp export hint is corrected against a run

**Both sentences `impexp-artifact` states about the console are wrong on all
four covered versions. The two feedback that report them are one card at
`normal`.**

The hint describes what `Export` can do and prescribes the command as the way to
get it. What the command does with the two arguments in between, the tables it
follows relations into and the filename, is where both errors sit. Nobody ever
read either off `ExportCommand`.

## Evidence

- Re-run on 2026-08-17 against the corpus as it is now.
  `bin/cli hints:probe "impexp:export sys_file_reference images export file distribution"`
  reaches `impexp-artifact` first at `appliesTo(19) + text(401)`, ahead of
  `fal-reading`, `sitepackage-initial-content` and `fal-testing`. The probe
  finds the hint, the server delivers it, and it says the wrong thing. So no
  rung above 1a applies.
- Nothing else covers it. `--include-related` and `files_fal` appear nowhere
  below `knowledge/` or `skills/`, and `getFileAbsFileName` appears once: in the
  sentence the second feedback reports.
- The export adds a related record only where the caller named its table.
  `Export::exportAddRecordsFromRelationsPushRelation()` guards on
  `inclRelation()`, which is true only for a table in `relOnlyTables` or for
  `_ALL`, and `ExportCommand` fills `relOnlyTables` from `--include-related`
  alone. So naming `sys_file_reference` under `--table` reaches the reference
  rows and stops there.
- The bytes hang off those records rather than off the references.
  `exportAddFilesFromSysFilesRecords()` iterates `header.records.sys_file`. So
  with no `sys_file` record added there is no `files_fal` part to write and
  nothing to put in the `.files` directory. That is what the feedback observed
  and what makes its proposed check the right one.
- The filename argument keeps its basename and loses its path. `ExportCommand`
  calls `setExportFileName(PathUtility::basename(...))`, so
  `EXT:site_distribution/Initialisation/data` becomes `data` and the export
  lands in the default import-export folder. The `EXT:` form the hint recommends
  is neither honoured nor rejected, and the success message names where the file
  actually went.
- The half the hint gets right is the other direction. `Import::loadFile()`
  resolves its argument through `GeneralUtility::getFileAbsFileName()`, so
  `EXT:<key>/Initialisation/data.xml` is the form that works for
  `impexp:import`.
- None of it is version-bound. `inclRelation()` reads the same on
  `.checkouts/12.4` at `31f881a212`, `.checkouts/13.4` at `fccbd407d8`,
  `.checkouts/14.3` at `627949e9dd` and `.checkouts/main` at `3a9f0b5e3c`. The
  TCA lookup moved to `TcaSchemaFactory` after 12.4 and the relation condition
  did not. The `basename()` call stands in `ExportCommand` on all four. So the
  correction carries no `since` or `until`.
- The session that reported paid for both. It shipped an export with no image
  bytes and a package with no `data.xml`, in a domain where the command answered
  `[OK]` each time.

## Decided

- Step 1a for both, and queued rather than closed on the spot. A run of the
  command establishes what the corrected sentences say about the minimal
  invocation and about the shape of a correct artifact.
  [`judging.rst`](../../documentation/records/judging.rst) puts that on the
  other side of the line from a wording fix.
- One card for the two, with both feedback in its `**Serves:**` line, and the
  same commit deletes `todo/open/2026-08-17-211418`. Both sentences are the same
  procedure in the same hint, corrected in one file from one run, and two cards
  would spin up an installation twice for one reading.
- `normal` rather than the `low` both cards arrived at. Two sessions reported
  one hint as wrong within half an hour. The failure mode is a distribution that
  ships without its images or without its content at all while every command
  reports success.
- Not `high`. Both workarounds are one argument and one `mv`, the session found
  them itself, and nothing else waits on the hint.
- Neither archived nor trimmed. Nothing below `knowledge/` or `skills/` answers
  any part of either observation today.
- The `EXT:` sentence gets a correction rather than a drop. It holds for
  `impexp:import`, which is the direction a session reads a distribution back
  in. To say which direction it applies to is what the second feedback asks for.
- The todo words the discriminator the corrected hint carries, and does not
  invent it. `feedback/2026-08-17-212800` asks for one on every procedural hint
  and names this one as its first example; this correction is that proposal's
  worked case, and judging it stays its own card.

## Assumed

- That one session wrote both feedback and the twenty-three beside them. They
  share a directory, a model, a subject and three quarters of an hour, and
  nothing in a feedback records a session. The second one says so in its own
  first paragraph, which is as close as the corpus gets.
- That `--include-related=sys_file` is the whole of the absent invocation.
  `inclRelation()` says it is what admits the record, and the run establishes
  whether the caller has to name `sys_file_metadata` beside it.
- That the backend module reaches the same wall. `ExportController` fills
  `relOnlyTables` from a form field that starts empty. So the hint's claim is
  not a module behaviour written up as a console one, but nobody has exported
  through the module to check.

## Wrong if

- The run finds that `--table=sys_file` alone brings the bytes along. The
  relation path would then be one route of two. The corrected sentence would be
  about which of them a distribution wants rather than about an absent argument.
- A later release resolves the export filename through
  `GeneralUtility::getFileAbsFileName()` like the import does. The correction
  would become version-bound at that release, and the sentence the hint carries
  today would have been right about the intent all along.
- A third feedback lands on `impexp-artifact` from a route neither of these two
  took. The hint would be a subject that has outgrown one entry rather than a
  file with two wrong sentences in it. The card would be a rewrite instead of a
  correction.
- The server delivers the corrected hint and a session still ships an export
  without its files. The gap would be in the wording or in the delivery, and
  this entry would have answered the expensive rung for a cheap problem.

## Confirmed on 2026-08-18

A session ran both sentences against the command they describe and both are
wrong as reported, over seven exports of one seeded tree. The hint's own
prescription wrote the references and no file. With the related-files option it
wrote the rows and the bytes beside them, and both runs answered `[OK]`.

The table named instead produced the first result byte for byte. That settles
the **Wrong if** that would have made this a correction about which of two
routes to take. A page tree holds no such rows, so there is one route, and
either option names the reference table.

The build of the installation found one defect of this repository's own. The
build asked for the project name of the default driver whatever driver it built.
So it refused an environment on a second database for a project root it does not
own.
