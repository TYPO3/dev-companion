---
date: 2026-09-01T21:05:29+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3-core-patch-development, typo3-core-patch-review, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# Keep the diff minimal: reviewers read diffs, not final files

## Observation

Migrating a local memory into the server: a standing correction from this user about patch shape.

Keep diffs as minimal as possible, because people have to review them. TYPO3 patches go through Gerrit; a large diff is harder to review and more likely to attract pushback. Unnecessary reformatting, style changes or array collapsing is noise that hides the actual change.

- Prefer editing over rewriting a file — use a targeted edit, not a full rewrite, for a file that already exists.
- Preserve existing formatting: expanded arrays, indentation style, and so on.
- Do not collapse multi-line arrays onto one line or the reverse unless that IS the change.
- Prefer expanded multi-line arrays over collapsed ones — one key per line means a diff marks the individual change instead of flagging the whole line.
- Consolidate tests by merging assertions into existing test methods rather than rewriting the file.
- When reducing tests, keep the existing fixture helpers and style and just remove the redundant methods.

In this same session the rule was violated in a way worth recording: reviewing his own change I "fixed" a namespace layout, a class's finality and its accessor visibility. All were deliberate, and the chained follow-up change proved it. Minimal-diff applies to review as much as to authoring — an observation about structure is a comment, not an edit.

## Query

Migrated from a local project memory file (feedback_minimal_diffs.md). A standing correction from this user on how patches are prepared.

## Suggestion

State this in the patch development skill as a property of the finished patch, and in the review skill as a constraint on what a reviewer may change versus merely report. The multi-line-array point is the concrete one and generalises: prefer the formatting that makes future diffs narrow, not the formatting that makes this file shortest. Related feedback already filed: reviewing a Gerrit change must read its chain before calling structure a defect.
