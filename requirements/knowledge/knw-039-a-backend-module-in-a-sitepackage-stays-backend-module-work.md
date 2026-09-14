---
id: R-KNW-039
title: 'A backend module in a sitepackage stays backend-module work'
status: held
heldBy:
  - HintsTest::aBackendModuleInASitepackageDoesNotBecomeFrontendWork
---

# R-KNW-039 — A backend module in a sitepackage stays backend-module work

**A backend module a sitepackage owns stays backend-module work.**

Its domains include PHP, and its registration hint ranks first. Generic mentions
of `sitepackage` and `records` do not pull in the package-layout,
initial-content or frontend-record-rendering guides. Those guides apply only
when the task asks for that layout, content shipment or frontend render. They do
not apply when the words merely name the owner and the data a backend module
reviews.

## From

A backend review module with actions, badges, icons and translated labels. Its
17 KB guide was full of frontend records and the complete sitepackage layout and
omitted PHP/module registration (2026-07-30).
