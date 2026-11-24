---
date: 2026-11-24T20:22:44+00:00
category: missing-knowledge
status: closed
closed: 2026-11-24
model: claude-opus-5-5
tool: typo3-core-patch-review, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# A core patch review does not check the code comments against ASD-STE100

## Observation

Task: review core change 97935 "[BUGFIX] Show hosts of sites below a web mount". The user then asked for a check of the comments against ASD-STE100.

The review skill has no surface for the language of comments and docblocks. The checklist does not name it. The report skeleton does not name it.

The guide "any/writing/the-prose-a-patch-carries" gives good rules for comment prose. It does not refer to ASD-STE100. It does not give word limits per sentence. It does not give a list of approved words.

The server has no ASD-STE100 dictionary. Thus the agent checked the approved words from memory. The agent told the user to verify these words manually. Examples are "reach" in the meaning "has access to", "thus", "ignore" and "unavailable".

The user wants ASD-STE100 as the standard for comment review. The server already uses ASD-STE100 for its own feedback records. Thus the standard is known here, but the review does not use it.

## Query

Change 97935, patch set 1. Comments in typo3/sysext/core/Classes/Site/WebMountSiteFinder.php: the class docblock, the @return text, and "// Ignore unavailable sites". typo3_rule_lookup documentId="any/writing/the-prose-a-patch-carries".

## Suggestion

Add a surface "Comments and docblocks" to the core patch review checklist and to the report skeleton.

Give the ASD-STE100 writing rules for comments in the prose guide or in a hint. Include the word limit per sentence, one topic per sentence, the active voice and simple tenses. Include the noun cluster limit and the rule against omitted words.

Give a short list of approved and unapproved words that comments in the core often use. Examples are reach, access, ignore, thus, available and handle. Show the approved alternative for each unapproved word.

Make the review say which word decisions come from the dictionary and which come from memory.
