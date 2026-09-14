---
id: R-KNW-065
title: 'Booting a declared installation is answered as its own subject'
status: held
restsOn: [D-KNW-054]
heldBy:
  - HintsTest::bootingADeclaredInstallationIsAnsweredBeforeThePhpFallback
---

# R-KNW-065 — Booting a declared installation is answered as its own subject

**A task that boots the installation a repository already declares gets what a
clone still owes it as its answer.**

That is five things. The schema an imported database is behind on, and the
caches the dump brought with it. The backend user nobody has the password for,
the host the site configuration names, and where the files should be. Every one
of them fails in silence. The schema is behind and nothing says so, and the
imported cache serves another installation's pages. The create-user step waits
on stdin inside a hook, and the site answers page-not-found at its own root. The
file records have no bytes behind them. A brief that answers this task with the
setup command answers the other half of the subject, the installation that has
to come into existence. A session that boots a clone reads it as the whole of
it.

## From

A session that booted a TYPO3 Composer project from a fresh clone, whose brief
came back with `datahandler-basics`, `fal-basics` and `public-assets`
(`feedback/2026-08-03-154508`). The change type half of that report landed as
`operations` and the `installation-operations` intent on 2026-08-03 and did not
move the hints. The four knowledge cards `D-SKL-012` put first landed the
install rather than the boot. The same query still reached the same four PHP
hints on 2026-08-03.
