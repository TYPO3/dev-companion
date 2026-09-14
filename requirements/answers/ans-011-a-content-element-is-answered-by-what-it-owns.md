---
id: R-ANS-011
title: 'A content element is answered by what it owns'
status: held
heldBy:
  - HintsTest::aRepeatableContentElementIsRoutedThroughWhatItOwns
---

# R-ANS-011 — A content element is answered by what it owns

**A content-element task gets what the element owns as its answer before it gets
how the element registers.**

What it owns is fields on `tt_content`, a child table of its own, a reference to
existing records, or a container of arbitrary content. Which one it is follows
from the editor workflow rather than from what a list can technically do.

The shapes that decide it, a carousel, a slider, an accordion, slides, reach
that answer without the task having to call itself a content element.

## From

A hero carousel designed out of generic record references and validated as
technically possible, with the ownership decision never forced (2026-07-30);
scenario `SKILL-04`.
