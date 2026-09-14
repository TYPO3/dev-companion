---
id: R-KNW-064
title: 'The Composer keys that install TYPO3 beneath an extension are answered'
status: held
restsOn: [D-KNW-053]
heldBy:
  - HintsTest::installingTypo3BeneathTheExtensionNamesTheInertKey
---

# R-KNW-064 — The Composer keys that install TYPO3 beneath an extension are answered

**An extension repository that installs TYPO3 beneath its own `composer.json`
gets three answers.**

Those are the keys that move the installation, the key that only warns, and the
root package's placement.

Two of the three are traps rather than absences. Composer takes
`extra.typo3/cms.app-dir` at write time and reports it at install time. So
`config/` and `var/` land in the versioned tree of a session that believed it
had moved them. A root require of `typo3/cms-cli` fails with a message that
names `typo3/cms-core`, so the line that looks wrong is not the line that is.
The placement is the third. Composer loads the root package from the Composer
root and installs nothing into `typo3conf/ext/`, which otherwise reads as a
broken installation.

## From

A session that gave a standalone extension repository a local environment. It
found all three by trial and reported them as absent knowledge
(`feedback/2026-08-03-162759`). The corpus named `extra.typo3/cms.web-dir` only
to say which directory the server serves. `app-dir`, `vendor-dir`, `bin-dir` and
`cms-cli` occurred nowhere below `knowledge/` (2026-08-03).
