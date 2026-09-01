---
id: D-DIS-021
title: A stale publication is put back where the server starts
date: 2026-08-29
status: open
coveredBy:
  - StdioServerTest::aProjectWhoseSkillsNobodyHasUpdatedHasThemPutBack
---

# D-DIS-021 — A stale publication is put back where the server starts

**A server starting in a project republishes the copies that have drifted,
because every other mechanism needed somebody to remember.**

`R-DIS-025` says a publication went stale and stops there. Nothing here had a
moment that arrives without a person deciding to bring it.

## Evidence

- Twelve projects on one machine held a publication on 2026-08-29 and all twelve
  had drifted. Every one of them had been told at every session start for weeks,
  and the notice had been acted on by nobody.
- `D-DIS-014` predicted this in its second **Wrong if**: the hook fires on
  `composer update`, and "the standalone checkout every knowledge session uses"
  moves without one. All twelve run against that checkout, `skills/` had 162
  commits since 2026-07-01, and no project had wired the hook.
- Its last **Assumed** names what is left over: "every other moment is somebody
  remembering". A command, a hook nobody wired and a notice nobody read are the
  same mechanism wearing three coats.
- What changed on 2026-08-29 is that a machine-wide client entry makes this
  server start in every project, in every session, unattended — the first moment
  in this design that nobody has to bring.
- The bytes were already this package's to write. `install` and `update` write
  exactly them, `R-DIS-024` has each published directory ignoring itself, and
  `InstallerRecordTest::updateWithoutAnAgentRefreshesEveryClientInstalledHere`
  has an edit to a published copy overwritten since 2026-08-08.

## Decided

- A start that finds the digest changed republishes what the record names, then
  says so on both channels.
- It adds nothing: the clients are the recorded ones and no client configuration
  is written. A project with no record is untouched, which is what keeps
  `R-DIS-011` — writing into a project is an explicit `install` — true of what
  put the publication there.
- A refresh that throws leaves the old notice and the server starts. Writing
  into somebody's project may not be the thing that stops one.
- `TYPO3_DEV_COMPANION_SKILL_REFRESH=off` keeps the notice and changes nothing,
  for a reader who wants the copies to move when they say so.
- The instructions say it in one sentence of the same length the notice had,
  because the client read the skills directory when the session opened rather
  than after this ran. That is measured rather than assumed, on 2026-08-29 in
  `E-NONE`: one published skill was taken out of the project, a session was
  started there, and the client listed thirteen while the refresh had written
  the fourteenth back at 09:48:42. The listing a session is given is built
  before this runs, so the sentence is the only thing that says so.

## Assumed

- That a published copy is nobody's to edit. Both commands already overwrite
  one, so a refresh takes nothing an update would have left.
- That two sessions starting at once in one project either write the same bytes
  or write them twice. What they write is decided by this package's own files,
  not by anything either session carries.

## Wrong if

- A client reads a skills directory while it is being replaced and loads half a
  publication. Nothing here locks, and the window is one directory copy wide —
  the reading above puts the client's own read before this runs, which is what
  makes the window narrow rather than what removes it.
- Somebody edits a published copy deliberately — to try a change to a skill in
  the project it runs in — and a server start takes it back before they have
  read the result.
- The refresh runs in a project whose record names a client that is gone, and
  writes a publication into a directory nobody reads any more.
