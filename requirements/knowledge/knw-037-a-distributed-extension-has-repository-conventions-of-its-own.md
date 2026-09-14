---
id: R-KNW-037
title: 'A distributed extension has repository conventions of its own'
status: held
heldBy:
  - HintsTest::aDistributedExtensionIsNotAnsweredWithTheProjectLayout
---

# R-KNW-037 — A distributed extension has repository conventions of its own

**A repository that is only the extension has conventions of its own, written
down as such.**

They are one unit rather than two, and the declared constraint rather than the
installed version as the statement of support. They are no committed lock file,
shipped dependencies where the extension also installs outside Composer, and a
browser suite that belongs to the package.

The project repository layout names what it is about, so nobody reads the two
for each other.

## From

`REVIEW-02`. The corpus had a hint for a project repository and none for a
distributed extension, and the review quoted the project one. It moved the
browser suite to a repository that does not exist and called the ignored lock
file non-reproducible (2026-07-31).
