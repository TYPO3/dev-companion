---
id: D-DIS-021
title: A stale publication is put back where the server starts
date: 2026-08-29
status: open
coveredBy:
  - StdioServerTest::aProjectWhoseSkillsNobodyHasUpdatedHasThemPutBack
---

# D-DIS-021 — A stale publication is put back where the server starts

**A server that starts in a project republishes the copies that have drifted,
because every other mechanism needed somebody to remember.**

`R-DIS-025` says a publication went stale and stops there. Nothing here had a
moment that arrives unless a person decides to bring it.

## Evidence

- Twelve projects on one machine held a publication on 2026-08-29 and all twelve
  had drifted. Every one of them had heard it at every session start for weeks,
  and nobody had acted on the notice.
- `D-DIS-014` predicted this in its second **Wrong if**: the hook fires on
  `composer update`, and "the standalone checkout every knowledge session uses"
  moves without one. All twelve run against that checkout, `skills/` had 162
  commits since 2026-07-01, and no project had wired the hook.
- Its last **Assumed** names what remains: "every other moment is somebody
  remembering". A command, a hook nobody wired and a notice nobody read are the
  same mechanism in three coats.
- What changed on 2026-08-29 is that a machine-wide client entry makes this
  server start in every project, in every session, unattended. That is the first
  moment in this design that nobody has to bring.
- The bytes were already this package's to write. `install` and `update` write
  exactly them, and `R-DIS-024` has each published directory ignore itself.
  `InstallerRecordTest::updateWithoutAnAgentRefreshesEveryClientInstalledHere`
  has an edit to a published copy overwritten since 2026-08-08.

## Decided

- A start that finds the digest changed republishes what the record names, then
  says so on both channels.
- It adds nothing: the clients are the recorded ones and it writes no client
  configuration. A project with no record stays as it is. That keeps
  `R-DIS-011`, a write into a project is an explicit `install`, true of what put
  the publication there.
- A refresh that throws leaves the old notice and the server starts. Writing
  into somebody's project may not be the thing that stops one.
- `TYPO3_DEV_COMPANION_SKILL_REFRESH=off` keeps the notice and changes nothing,
  for a reader who wants the copies to move when they say so.
- The instructions say it in one sentence of the same length the notice had. The
  client read the skills directory when the session opened rather than after
  this ran. That is a measure rather than an assumption, on 2026-08-29 in
  `E-NONE`. One published skill left the project, a session started there, and
  the client listed thirteen while the refresh had written the fourteenth back
  at 09:48:42. The listing a session gets exists before this runs, so the
  sentence is the only thing that says so.

## Assumed

- That a published copy is nobody's to edit. Both commands already overwrite
  one, so a refresh takes nothing an update would have left.
- That two sessions which start at once in one project either write the same
  bytes or write them twice. This package's own files decide what they write,
  not anything either session carries.

## Wrong if

- A client reads a skills directory mid-replacement and loads half a
  publication. Nothing here locks, and the window is one directory copy wide.
  The measure above puts the client's own read before this runs, which is what
  makes the window narrow rather than what removes it.
- Somebody edits a published copy deliberately, to try a change to a skill in
  the project it runs in. A server start takes it back before they have read the
  result.
- The refresh runs in a project whose record names a client that is gone, and
  writes a publication into a directory nobody reads any more.
