---
date: 2026-10-22T10:57:11+00:00
category: missing-knowledge
status: closed
closed: 2026-10-22
model: claude-opus-5-5
tool: typo3-core-patch-development, typo3_rule_lookup, typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# No procedure to reply to a review thread on Gerrit and keep it unresolved

## Observation

Task: amend own core change 96975 after review, push it, answer three reviewer comments, do not resolve them.
The development skill says "address a reviewer's comment in the patch or answer it in the review". It does not say how to answer. The review skill says comments stay with the person. No guide carries the reply step.
I worked it out myself. I wrote a ReviewInput JSON with "comments", "in_reply_to" and "unresolved": true. I posted it with "ssh -p 29418 <user>@review.typo3.org gerrit review --json 96975,1". I posted to patch set 1, because the threads were on patch set 1. That was a guess. It worked.
What worked: typo3_gerrit_lookup gave each comment id, its thread and threadUnresolved. The ids were the in_reply_to values. A second lookup confirmed three replies in the right threads, all unresolved. Keep these fields.

## Query

Task text: "ok pushe es bitte und antworte auf ollis kommentare, resolve sie nicht" after amending change 96975.

## Suggestion

Add a section to core/contribution/gerrit-workflow: "Answer a review comment". Give the ssh gerrit review --json form and the REST form. Say that in_reply_to takes the comment id that typo3_gerrit_lookup returns. Say on which patch set to post the reply. Say that unresolved must be set to true explicitly to keep the thread open, and false to resolve it. Say how to verify the result with typo3_gerrit_lookup (threadUnresolved). Link it from the "Amending after review" part of typo3-core-patch-development.
