---
id: D-ANS-135
title: The reference scan is asked for the resources an answer names
date: 2026-09-02
status: open
coveredBy:
  - LabelSearchTest::onlyTheResourcesAnAnswerNamesAreScannedFor
  - LabelSearchTest::theFileReadingResolvesNoReference
---

# D-ANS-135 — The reference scan is asked for the resources an answer names

**`LabelReference::find()` runs over the resources a query matched, not over
every XLF file the installation ships.**

## Evidence

Measured against `.environments/e-site-14.3`, a stock installation of 25
packages with one project extension, on 2026-09-02:

- The scan walks 8398 source files and reads 42 MB of them: 0.45 s to iterate
  and 0.71 s to read.
- It tested each of those against 354 resources, at up to three containments per
  resource. That inner loop, not the reading, was the cost.
- `Labels::all()` took 0.05 s before `D-ANS-134` was implemented and 2.35 s
  after it.

Through the tool, against the same installation, before and after this change:

| query                 | before | after  | resources reported |
| --------------------- | ------ | ------ | ------------------ |
| `save document`       | 2.81 s | 0.94 s | 1                  |
| `save`                | 2.48 s | 0.66 s | 6                  |
| `title`, `limit: 200` | 2.42 s | 1.04 s | 41                 |

Each answer names the same resources with the same diagnostics as before. The
times include the console call, which is the larger part of what is left.

## Decided

- `Labels::all()` carries `absolute` and `implicitReferences` per label and
  resolves no reference.
- `LabelLookup::resourceDiagnostics()` asks `LabelReference::find()` for the
  resources it is about to report, once the query has matched and the limit has
  been applied.

What this buys is not the factor above but the shape of the cost: it grows with
the answer, which `limit` bounds, rather than with the installation, which
nothing bounds. A project holding a thousand XLF files pays what a stock
installation pays.

## Assumed

- Every consumer of a reference wants it for a resource it is reporting.
  `resourceDiagnostics()` is the only reader.

## Wrong if

- A caller needs the reference of a resource that no label of its query matched.
  Then the scan is asked for a resource set the answer does not name, and the
  saving goes with it.
- The walk and the reading become the cost rather than the containments — a
  project whose sources outgrow its label files. Then what has to go is the
  reading of every file, and narrowing the resources buys nothing more.
