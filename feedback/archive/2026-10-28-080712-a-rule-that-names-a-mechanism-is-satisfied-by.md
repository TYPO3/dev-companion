---
date: 2026-10-28T08:07:12+00:00
category: idea
status: closed
closed: 2026-10-28
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# a rule that names a mechanism is satisfied by code that fails; name the condition instead

## Observation

Task: build a sitepackage on TYPO3 14.3, then judge it for disabled users.

css-accessibility-states says that keyboard focus must be visible with :focus-visible or an equivalent pattern. I wrote exactly that rule. The token behind it reaches 1.10 against the utility bar, 1.10 against the footer, 1.57 against the hero and 2.28 against the main navigation. WCAG 2.2 asks for 3:1. The ring was invisible where most of the links are, and the rule was satisfied.

The statement names a mechanism. A mechanism is satisfied by using it. A condition is satisfied only by meeting it, and a condition can be measured: the focus indicator reaches 3:1 against the surface it lands on, which is arithmetic over two declared values.

content-elements has the same shape from the other end. It names two stale pre-v14 idioms in the addRecordType call and not the third. A list of what is stale reads as the whole list, so the reader stops checking.

Both cost the same way. The reader believes the rule is met, and nothing says otherwise.

The model cannot be changed and more models will be run against this server. A rule that a reader can satisfy while failing will be satisfied that way by some of them.

## Query

typo3_hint_lookup ids=[css-accessibility-states] targetVersion=14, and ids=[content-elements] targetVersion=14. Observed against a sitepackage declaring --focus #1a1a1a with surfaces #14261f, #06603f and #04452d.

## Suggestion

Where a statement can name a measurable condition, name that rather than the mechanism that usually produces it. "Focus is visible with :focus-visible" becomes "the focus indicator reaches 3:1 against the surface it sits on, which one token cannot do for a light surface and a dark one".

Where a statement lists cases, say whether the list is exhaustive. "These are the stale idioms in this call" and "these are two of them" are read the same way and mean different things.

A condition also gives the reader a check to run. The contrast of a declared colour against a declared background is arithmetic, and the reader can do it without a browser.
