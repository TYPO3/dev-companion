---
id: R-KNW-034
title: 'Configuration is placed by the reach of its value'
status: held
heldBy:
  - HintsTest::aSettingIsPlacedByTheReachOfItsValue
  - HintsTest::siteScopedConfigurationIsOfferedOnlyWhereSiteSettingsExist
---

# R-KNW-034 — Configuration is placed by the reach of its value

**The reach of a value places its configuration. Site-specific values are site
settings, one-per-installation values are extension configuration, and a
scheduler task's parameters live with that task.**

The site-settings form binds to the versions that have it.

## From

A per-site storage pid put into a new `ext_conf_template.txt` after the session
had already read the same sitepackage's set settings (2026-07-30).
