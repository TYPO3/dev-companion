---
id: R-DIS-021
title: 'The client entry is rewritten when the project outgrows it'
status: held
heldBy:
  - InstallerTest::codexUpdateRewritesTheSectionAndKeepsTheRestOfTheFile
  - InstallerTest::updateRefusesToReplaceAnotherCommand
  - InstallerTest::updateRewritesTheEntryAProjectHasOutgrown
---

# R-DIS-021 — The client entry is rewritten when the project outgrows it

**Install and update both write the client entry, and both refuse only an entry
that starts something other than this server.**

What belongs in the entry is a property of the project, not of the run. A
project that required this package after its first install needs a different
entry than the one that is there. So does one that gained a DDEV configuration
since. A check instead of a write left that project with a message and no
command that would fix it. `install` refuses an entry it did not just write. The
line runs at the server the entry starts, so an entry that belongs to somebody
else still never gets replaced.

## From

`update` in `E-SITE` reported
`.mcp.json has a different or missing typo3-dev-companion entry` after the
project required the server, and `install` refused the same entry (2026-07-31).
