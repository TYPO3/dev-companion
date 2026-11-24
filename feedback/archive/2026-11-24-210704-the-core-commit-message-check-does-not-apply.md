---
date: 2026-11-24T21:07:04+00:00
category: wrong-answer
status: closed
closed: 2026-11-24
model: claude-opus-5-5
tool: typo3_commit_message_guide, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# The core commit message check does not apply the body rules of its own document

## Observation

Task: amend core change 97935 and write the commit message of patch set 2.

typo3_commit_message_guide checked the message twice with workflow "core". It reported only reflow information. The message had five paragraphs and a bullet list. It named new methods and steps that the diff already shows. It had a chain of sentences that start with "Thus".

The document "core/contribution/commit-messages" forbids this. Its section "Body" says the body has one or two sentences. It says to write prose rather than a list. It says to leave out what the diff shows. The check did not apply these rules. Thus the agent took the message as correct and pushed it.

The user also wants these rules, and the document does not have them yet:
- No list in the body.
- No long paths. This includes file paths and fully qualified class names.
- No unnecessary prose. Each sentence must give information that a reader needs.
- The body describes exactly what the change does. A vague statement is not sufficient.

## Query

typo3_commit_message_guide workflow="core", isBreaking=false, isDeprecation=false, message = patch set 2 of change 97935 "[BUGFIX] Show hosts of sites below a web mount" (body of five paragraphs with a bullet list).

## Suggestion

Make typo3_commit_message_guide report a warning for each of these conditions in a core body:
- The body has more than two sentences.
- The body contains a list item, a line that starts with "-", "*" or a number.
- The body contains a file path or a fully qualified class name.
- The body names a method, a class or a field that the change adds, without a reason.

Add these rules to the section "Body" of "core/contribution/commit-messages":
- No lists.
- No long paths.
- No unnecessary prose.
- Describe exactly what the change does, as behaviour, not as a list of code changes.

Give one rejected body and one accepted body as examples. Change 97935 patch set 2 is a good rejected example.
