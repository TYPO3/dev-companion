---
id: R-GUI-008
title: 'A brief states what the change is for before its steps'
status: held
restsOn: [D-GUI-005]
heldBy:
  - HintsTest::everyBriefOpensOnThePremiseADefectIsJudgedBy
---

# R-GUI-008 — A brief states what the change is for before its steps

**Every task brief opens on the premise: content changes, so the site has to
deliver the current version, and that outcome judges a defect.**

The steps under it are all about the code, and what the change does to the
editor and the visitor decides which of them matter. A brief that starts at
"confirm the branch" answers a bug report by whether somebody used the code
correctly. That is a question with its own consistent answers and not the one
the product asks.

## From

A core session on Forge #105403 that assessed the report as an API question.
That is whether the value passed to `f:image` is of the type the argument
accepts. It committed a clearer exception for it, where the defect was that an
editor who replaced the image went on to receive the old one. The same premise
was absent from three further turns the user corrected
(`feedback/2026-08-02-145043`, 2026-08-02).
