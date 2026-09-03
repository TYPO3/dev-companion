---
date: 2026-09-03T23:55:30+00:00
category: bug
status: open
model: claude-opus-5[1m]
directory: /home/benji/projects/site-tierheim
---

# typo3_feedback_record fails whenever the suggestion parameter is passed

## Observation

Task: file feedback at the end of a session spent on a TYPO3 v14 project - TCA work, a site audit, content repairs.

Every call carrying the optional "suggestion" parameter came back as "Error while executing tool", with no message beyond that. Five filed in one batch failed; the same one repeated alone failed; the same one again without "tool" and without "query" but still with "suggestion" failed. The identical feedback with the suggestion text folded into the observation was written without complaint.

Bisected in this order:
1. observation + model, one line: written.
2. observation + model + category + subject, one line: written.
3. observation + model + category + subject + suggestion, observation in several paragraphs: failed.
4. observation + model + category + subject, observation in several paragraphs, no suggestion: written.

So it is not the batch, not the length, not the paragraph breaks in the observation, and not the category or subject. What the failing calls have in common is "suggestion". Whether "query" fails on its own is untested - the first batch carried both, and I stopped passing either once the pattern was clear.

The cost is quiet: the tool description asks for what the server should have answered instead, so a caller writes it, gets an error with no reason, and either drops the feedback or files it without the half that says what to do. Two probe files from the bisection should be deleted: feedback/2026-09-03-235432-test-whether-this-server-can-record-feedback-at.md and feedback/2026-09-03-235457-probe-two-category-and-subject-together.md, with their todo entries T-260903-6c27 and T-260903-bc9b.

What would help: whatever writes the suggestion section into the markdown is the place to look, and the error should name the parameter it choked on rather than reporting that the tool failed.
