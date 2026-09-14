---
id: R-KNW-041
title: 'A preview template answer says what the template is handed'
status: held
restsOn: [D-KNW-020]
heldBy:
  - HintsTest::aPreviewTemplateSaysWhatItIsHandedAndWhatAFieldResolvesTo
---

# R-KNW-041 — A preview template answer says what the template is handed

**A backend preview answer names the variables the template receives on the
target major, and what a field read off the record resolves to.**

The registration of the template is where the corpus used to stop, and it is the
half a session does not need help with. The other half binds to the major. The
row's columns are variables of their own until the Record API replaces them, and
after that everything comes off one record.

The TCA type of a field decides what it resolves to. So the answer names which
types come back as records, select with a relation, group, inline, category,
file, and what a static select stays instead. It states the two ways the
template renders nothing in silence with them. One is a column read as a bare
variable where only the record has an assignment. The other is a path that is
not a schema field of the record type, which resolves to null rather than
throws.

## From

A session that wrote a TYPO3 14 preview template that had to show assigned
related groups. It found the registration and nothing else. It could not
determine how {record.header} resolves given a class with neither `__get` nor
`ArrayAccess`, and it guessed a template it could not verify (2026-08-01).
