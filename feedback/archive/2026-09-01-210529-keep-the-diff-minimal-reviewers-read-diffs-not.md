---
date: 2026-09-01T21:05:29+00:00
category: idea
status: closed
closed: 2026-09-01
model: claude-opus-5
tool: typo3-core-patch-development, typo3-core-patch-review, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# Keep the diff minimal: reviewers read diffs, not final files

## Observation

Answered on 2026-09-02, in `core/contribution/rules` rather than in the two
skills this suggested. Its `## Code Style` section now says that the diff is
what the patch is shaped for: edit rather than rewrite, leave the form of what
was not touched, do not collapse or expand an array unless that is the change,
write a new one expanded so the next diff marks a key rather than a line, and
extend a test by merging an assertion into the one that covers the case.

Neither skill is edited and neither states it. Both route to that document, and
a copy in a skill is one no release of this server corrects — which the review
skill says of itself in its second paragraph. The review half this asked for is
already carried there, and the case that actually went wrong in the reporting
session — a change to something deliberate while a rework was authorised — is
`feedback/2026-09-01-210110` and its own card. `D-KNW-141` carries the reading,
including what the core's fixer does and does not decide about an array.
