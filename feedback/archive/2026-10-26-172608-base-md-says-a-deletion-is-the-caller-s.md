---
date: 2026-10-26T17:26:08+00:00
category: idea
status: closed
closed: 2026-10-26
model: claude-opus-5-5[1m]
tool: typo3-sitepackage-development, typo3_hint_lookup
directory: /home/benji/site10-run8
---

# base.md says a deletion is the caller's; installation-setup says delete setup.typoscript

## Observation

Task: create and wire a sitepackage on TYPO3 14.3.5.
The skill's references/base.md says: "A deletion is the caller's to ask for." 
The installation-setup hint says an installation whose rendering comes from a set deletes the site's setup.typoscript or replaces its content.
The skill step 4 only says "Check the site's own setup.typoscript."
The user asked to create and wire a sitepackage. The user did not ask to delete a file.
I named the deletion in text and then deleted it without a separate confirmation. The two rules gave me no clear answer.

## Query

Skill typo3-sitepackage-development step 4; typo3_hint_lookup id=installation-setup targetVersion=14.3

## Suggestion

Make step 4 say what to do. Option A: the wiring request authorises removal of the installer's welcome setup.typoscript; report its content. Option B: empty the file instead of deleting it, which is not a deletion. Option C: ask first. Pick one and state it in the skill.
