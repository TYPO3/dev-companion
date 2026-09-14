---
id: R-DIS-002
title: 'The packages are read from the declared vendor directory'
status: held
heldBy:
  - InstanceTest::aProjectThatMovedItsVendorDirectoryIsStillFound
---

# R-DIS-002 — The packages are read from the declared vendor directory

**The server reads the packages of a Composer installation from the vendor
directory it declares, not from the default.**

## From

The extension checkout with `config.vendor-dir=.build/vendor` that got the
report "no installation found" (2026-07-29).
