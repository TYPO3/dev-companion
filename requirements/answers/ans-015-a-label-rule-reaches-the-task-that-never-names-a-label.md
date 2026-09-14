---
id: R-ANS-015
title: 'A label rule reaches the task that never names a label'
status: held
restsOn: [D-ANS-024]
heldBy:
  - LabelSearchTest::aCallerAboutToWriteAUnitIsToldItsSourceLanguage
---

# R-ANS-015 — A label rule reaches the task that never names a label

**A task that will add an XLF trans-unit learns the source language of the file
it writes into. It learns it without labels, XLF or translation in its own
words.**

The rule itself is `R-KNW-033` and this does not restate it. What this demands
is that it arrive. Every route through `knowledge/` opens on vocabulary the
caller has to supply first. A match reaches a hint, and the words of the task
text reach the `labels` intent. A session that describes its work as the work, a
content element, a backend module, a plugin, describes something that will need
labels. That is the point at which the source language is still cheap to get
right.

A caller names labels once it already knows they are a subject with rules of
their own. A rule reachable only from there reaches the sessions that would not
have broken it.

So `typo3_label_lookup` carries it. The answer states the source language, where
a translation goes, and that a session corrects a source file which is not
English in place. That tool is what a session calls between the decision to add
an element and its first unit. That makes it the one route a task that names no
label still takes (`D-ANS-024`).

## From

`feedback/2026-08-01-003313` (2026-08-01), a TYPO3 14 testimonials session in
`/home/benji/projects/site-new` that added `backend_fields.xlf` and
`messages.xlf` units in German because the site is German-only.
`feedback/archive/2026-07-30-185539` came before it, a forward run of EXT-04
that did the same in a sitepackage whose XLF files were German. Between the two,
the rule landed and the second report arrived anyway.

Measured on 2026-08-02:
`bin/cli hints:probe "add a testimonials content element to the sitepackage"`
reaches `content-elements`, `sitepackage-layout` and `frontend-page-rendering`.
The word labels in the same query is what brings `language-files` back.
