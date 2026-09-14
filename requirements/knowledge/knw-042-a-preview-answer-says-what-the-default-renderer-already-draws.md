---
id: R-KNW-042
title: 'A preview answer says what the default renderer already draws'
status: held
restsOn: [D-KNW-021]
heldBy:
  - HintsTest::aPreviewAnswerSaysWhatTheDefaultRendererAlreadyDraws
---

# R-KNW-042 — A preview answer says what the default renderer already draws

**A backend preview answer states that the Fluid template replaces the content
half only. It names the header and footer parts the default renderer draws
around it.**

The registration of the template says where the output goes and not what is
already on the page above it. The renderer draws the header before it dispatches
the event the Fluid renderer listens on. That listener sets the content alone,
so nothing a template writes can remove or move the header.

The answer names the parts by field rather than as "the header", because that is
what decides which line a template may write. A session told only that a header
exists writes `{record.subheader}` and duplicates the second part instead of the
first.

The split binds to no major. It holds wherever `wrapPageModulePreview()` gets a
header rendered apart, so the statement carries no version of its own.

It also has to be reachable from the symptom, which is the form the question
arrives in. That is a header rendered twice, seen in the page module, by
somebody who does not yet know which renderer drew the other one.

## From

A session that copied the manual's preview-template example verbatim and shipped
a duplicate header into the page module. The default renderer already renders
the record label into `element-preview-header` (2026-08-01).
