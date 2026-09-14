---
id: R-DIS-015
title: 'The DDEV client entry names an entrypoint that exists'
status: held
heldBy:
  - InstallerTest::aProjectThatNeverRequiredTheServerKeepsTheAbsolutePath
  - InstallerTest::ddevProjectNamesTheEntrypointAtTheBinDirectoryItDeclares
---

# R-DIS-015 — The DDEV client entry names an entrypoint that exists

**The DDEV client entry names this server's entrypoint at the `config.bin-dir`
the project declares. A project that never required the server keeps the
absolute host entrypoint instead.**

The container sees the project directory, so a path that is not below it is a
path that is not there.

## From

The naming of `E-EXT`: `install --agent=claude` in an extension repository with
`"bin-dir": ".build/bin"` wrote `ddev exec php vendor/bin/typo3-dev-companion`.
That file does not exist in that checkout, and nothing reported it until a
client tried to start the server (2026-07-31).
