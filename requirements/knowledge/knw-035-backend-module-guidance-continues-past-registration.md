---
id: R-KNW-035
title: 'Backend-module guidance continues past registration'
status: held
heldBy:
  - HintsTest::aBackendModuleNamesItsShortcutApiAndPostRedirect
---

# R-KNW-035 — Backend-module guidance continues past registration

**Backend-module guidance continues past registration.**

It names the version-bound shortcut API the doc header uses and requires a 303
redirect after a state-changing POST so the browser follows with GET.

## From

A module that added the superseded shortcut button by hand and answered its
import POST with a redirect that could repeat it (2026-07-30).
