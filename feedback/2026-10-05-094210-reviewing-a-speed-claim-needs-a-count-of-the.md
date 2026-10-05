---
date: 2026-10-05T09:42:10+00:00
category: missing-knowledge
status: open
model: claude-opus-5-5
tool: typo3_rule_lookup, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# reviewing a speed claim needs a count of the expensive calls, not only wall time

## Observation

Task: review change 96353. The commit claims "styleguide:generate -c tca drops from ~13s to ~4s" because it hashes each password once.
I read core/testing/timing-a-code-path whole. It measures time per call of one path.
First I timed the whole CLI command on ddev/MariaDB: 24.1 s to 16.7 s. I reported that "the claim does not reproduce".
The user asked: "did you measure the right thing? It is about the password hashes."
That was correct. The claim was about how often the expensive operation runs.
I wanted a temporary counter in AbstractArgon2PasswordHash. The auto-mode classifier in my client blocked the edit to a core crypto class.
I counted distinct salted $argon2 values in the database instead: 10 on the parent, 2 on the patch. A hash took 420 ms.
Then I timed the same command on SQLite (10.1 s to 3.7 s) and in the e2e setup (10.7 s to 4.4 s). Those matched the claim.
No document told me to count calls, to measure where the author measured, or to separate one cause from another.

## Query

typo3_rule_lookup documentId=core/testing/timing-a-code-path; review of a "[TASK] Speed up ..." patch with a stated before/after number.

## Suggestion

Add a section "Checking a patch's performance claim": (1) find the expensive operation the message names and count its invocations on both sides, (2) count without editing core classes, for example with a decorated service in a functional probe or a side effect like distinct salts, (3) measure in the environment the author used, usually SQLite or the e2e setup, (4) turn each part of the patch off once to give each cause its own share. Link it from typo3-core-patch-review.
