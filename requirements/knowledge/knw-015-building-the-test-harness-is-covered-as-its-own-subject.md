---
id: R-KNW-015
title: Building the test harness is covered as its own subject
status: held
heldBy:
  - HintsTest::aProjectExtensionIsToldHowToGetASuiteAtAll
---

# R-KNW-015 — Building the test harness is covered as its own subject

**Where a core answer assumes a harness the core already has, the build of that
harness is a subject of its own.**

The conventions of a core test transfer to a project extension unchanged.
Everything between `composer require` and the first green run does not exist
there and is the larger half of the work.

## From

A session that took `core-tests` into a project and paid for the phpunit
boilerplate, the database credentials and the document-root-relative extension
paths. It paid for the absent `SiteBasedTestTrait` and a `sys_template` that
dropped the site set TypoScript in silence (2026-07-29).
