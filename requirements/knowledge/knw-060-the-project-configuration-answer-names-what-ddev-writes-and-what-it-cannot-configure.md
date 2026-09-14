---
id: R-KNW-060
title: 'The project configuration answer names what DDEV writes and what it cannot configure'
status: held
restsOn: [D-KNW-049]
heldBy:
  - HintsTest::theDdevSettingsAnswerNamesEverySectionItGenerates
---

# R-KNW-060 — The project configuration answer names what DDEV writes and what it cannot configure

**The project-configuration answer names every section DDEV's settings
management generates into `config/system/additional.php`. It says that its
generator configures its own database container and nothing else.**

The database section alone makes the two ways out of that file look
interchangeable. They are not. To take the file over means to supply the image
processing, the mail transport and the `SYS` block as well. The trusted hosts
pattern in that block is what an installation stops to answer without. An
installation whose connection comes from somewhere else, SQLite or an omitted
database container, is the case the generator has no variant for. So the answer
says that a generated file is not among the ways out there. It says which of the
two that are keeps the sections DDEV had right.

## From

`feedback/2026-08-03-162858` (2026-08-03), a session that brought a TYPO3 14.3.5
instance up under DDEV v1.25.1 for an extension, on SQLite with
`omit_containers: [db]`. It read the statement that names the database settings,
disabled settings management, and wrote that half back. It got
`UnexpectedValueException` 1396795884 for the trusted hosts pattern nothing had
told it about.
