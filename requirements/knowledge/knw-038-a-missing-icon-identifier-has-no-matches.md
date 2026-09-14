---
id: R-KNW-038
title: 'A missing icon identifier has no matches'
status: held
heldBy:
  - IconLookupTest::aMissingIdentifierHasNoMatchesEvenWhenRelatedIconsExist
---

# R-KNW-038 — A missing icon identifier has no matches

**The validation of a complete backend icon identifier is exact.**

An absent identifier has `matchCount: 0` in structured data even when the answer
offers related identifiers, and those carry a separate `suggestionCount`.
Categories in front such as `actions-` and `content-` describe the icon's use.
They do not by themselves make every icon in that category a match or a
suggestion.

## From

`actions-definitely-does-not-exist` correctly described as absent in text while
its structured answer claimed 556 matches from the `actions-` prefix
(2026-07-30).
