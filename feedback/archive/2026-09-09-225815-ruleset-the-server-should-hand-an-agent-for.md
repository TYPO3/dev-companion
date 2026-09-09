---
date: 2026-09-09T22:58:15+00:00
category: missing-knowledge
status: closed
closed: 2026-09-09
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_hint_lookup, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# Ruleset the server should hand an agent for code comments, with before and after examples

## Observation

Task: fix Forge #107495 in a core checkout. The patch was correct after one round. Its comments took three rounds, because the user had to reject them twice.

An earlier feedback from the same session asked for "a rule on the prose a patch carries". That was too vague to act on, and the user said so. What follows are the rules themselves, derived from the sentences that were actually rejected.

The rejected sentences, and what was wrong with each:

1. "The expression is not the client's: it is handed over in the validation rule, so that the browser evaluates what DataHandler stores the value by."
   The user called this "vollkommen unverstaendlich". Four defects in one sentence: it opens by negating instead of stating; the colon carries the real content as a punchline; "is handed over" names no actor; "what DataHandler stores the value by" ends on a preposition.
   Plain version: "The server sends the pattern in the validation rule. This test only checks that the client applies it."

2. "keeps the browser from ever disagreeing with the two ends below"
   "the two ends" is a metaphor invented for the comment. "below" pointed at nothing, because the docblock was the last thing in the file above the class.

3. "The four below are valid to GeneralUtility::validEmail() and are narrowed away on purpose"
   "valid to" is wrong English. "narrowed away" is invented vocabulary.

4. "The syntax of a stored value is narrower than what may be sent mail"
   Compressing the sentence broke the grammar.

5. The same mechanism, that PHP emits a pattern and the browser applies it, was explained in three files: the PHP that emits it, the TypeScript that consumes it, and the test.

The cause is not carelessness about a single comment. A model writes in the register it is given, and the briefs of this server are written in long clause chains with fronted subordinate clauses. An agent reading them produces the sentences above.

## Query

Whole session: fix Forge #107495 in a TYPO3 core checkout, write tests and a Breaking changelog entry, commit it. The comment prose was rejected twice by the user before it was accepted. No brief or hint returned by typo3_task_guide or typo3_hint_lookup for the touched paths constrained how a comment should read.

## Suggestion

Hand these rules to the agent from typo3_task_guide, for every changeType that writes files. They are written to be pasted verbatim.

Rules for comments and docblocks:

1. Say what is, not what is not. "The server sends the pattern", never "The pattern is not the client's".
2. Name the actor. Every sentence has a concrete subject: DataHandler, FormEngine, the browser, this test. Avoid the passive where it hides who acts.
3. One thought per sentence. If a sentence needs a colon, a dash or a semicolon to hold its second half, split it.
4. Use a colon for a list or a definition. Never as a reveal.
5. No trailing prepositions. Write "the pattern DataHandler applies on save", not "the pattern DataHandler stores the value by".
6. Do not point at positions. "above", "below" and "here" go stale. Name the class, the method or the file.
7. Use the words the codebase uses. No metaphors invented for the comment, such as "both ends" or "narrowed away".
8. Prefer a longer correct sentence over a short broken one. Compression that damages grammar is not brevity.
9. One fact, one place. Explain a mechanism where it starts, and refer to it by name elsewhere. Three files explaining the same handover is two files too many.
10. Say why, not what. If the comment can be deleted and nothing is lost, delete it.
11. Length is a ceiling, not a target. Two lines for an inline comment. A docblock over three short paragraphs is describing a design, and that belongs in the changelog entry.

Self-check before the patch is done: read each comment once, at reading speed. If you have to go back to parse it, rewrite it. That check catches every sentence listed in the observation.

One more thing follows from this. The briefs and checklists this server returns are themselves written in the register the rules forbid, for example "a task that writes nothing is not going to call anything" or "the property is what the task produces". Rewriting the guidance in plain sentences is the cheapest way to improve what agents write, because a model matches the register it is handed. A rule that says "write plainly", delivered in dense prose, is contradicted by its own delivery.
