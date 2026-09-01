---
date: 2026-09-01T21:05:50+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_hint_lookup, typo3-extension-testing
directory: /home/benji/projects/typo3-cms
---

# Pin the full serialized SVG with assertSame; never assert SVG output with contains

## Observation

Migrating a local memory into the server: a standing correction from this user about testing SVG output.

When writing tests for SvgDocument, SvgSanitizer or inline SVG output, always assert against the FULL expected SVG string with assertSame(). Do not use assertStringContainsString or assertStringNotContainsString for SVG assertions.

Why: partial matches hide regressions. An escape, an attribute reordering, whitespace drift or a namespace change slips past a contains check but shows up in an exact comparison. The SVG pipelines here are security-sensitive, so an exact-match test is a tight contract rather than a brittle one.

How to apply: when adding or reviewing SvgDocument test cases exercising sanitized(), toInlineMarkup() or toXmlString(), pin the full serialized output. Where the expected string is not known in advance, run the pipeline once, capture the canonical output, and encode that verbatim into the assertion.

One boundary worth recording, found in this session: the same reasoning does NOT transfer to markup carrying a cache-busting value. Icon markup rendered through IconFactory embeds a sprite URL with a numeric cache buster, so a full-string assertion on it pins a value that changes for unrelated reasons. The rule is about output the pipeline fully determines.

## Query

Migrated from a local project memory file (feedback_full_svg_string_compare.md). A standing correction from this user on tests for SvgDocument / SvgSanitizer.

## Suggestion

Add to the testing hints, scoped to SVG and sanitiser output rather than to assertions in general, and carry the boundary with it — otherwise the rule gets generalised to all rendered markup and produces tests that break on a cache buster. The capture-the-canonical-output procedure is the usable half and should be stated as a step.
