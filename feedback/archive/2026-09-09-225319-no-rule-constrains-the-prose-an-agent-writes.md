---
date: 2026-09-09T22:53:19+00:00
category: missing-knowledge
status: closed
closed: 2026-09-09
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3-core-patch-development, typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# No rule constrains the prose an agent writes into code comments, tests and changelogs

## Observation

Task: fix Forge #107495 (FormEngine does not mark invalid type=email values) in a core checkout, which grew into a contract change with a new class, tests and a Breaking changelog entry.

The guidance covers what a patch must contain — changelog obligation, test layers, commit trailers, release lines — and says nothing about how the text inside it should read. The core's own AGENTS.md has one sentence ("Only add code comments when they add meaning"), which answers whether to write a comment but not how long or how plain it should be.

The result was that every comment I wrote had to be rewritten afterwards, by the user, as a separate review round. Concrete defects in my first draft, all in a single patch:

- A 13-line class docblock where 6 lines carried the content; three of the sentences restated each other in different words.
- Cross-references to positions that do not exist: "keeps the browser from ever disagreeing with the two ends below" — nothing is below the docblock.
- Broken English from over-compression: "The four below are valid to GeneralUtility::validEmail()", "narrower than what may be sent mail".
- Trailing-preposition constructions repeated as a mannerism: "the expression DataHandler stores the value by" appeared in four different files.
- The same fact explained three times, once in the PHP that emits a value, once in the TypeScript that consumes it, once in the test.
- A 4-line comment covering two unrelated facts where the non-obvious half was one line.

None of this is TYPO3-specific, but it is produced reliably by an agent working from this server's briefs, because the briefs are written in a dense, clause-heavy register and a model matches the register it is given. The cost is real: a reviewer on review.typo3.org reads these comments, and a patch that needs a style pass on its prose burns a review round.

## Query

typo3_task_guide with task="Fix FormEngine client-side validation not marking invalid values in TCA type=email fields as errors", changeType="bugfix", targetVersion="15", paths=[typo3/sysext/backend/Classes/Form/AbstractNode.php, Build/Sources/TypeScript/backend/form-engine-validation.ts, typo3/sysext/core/Classes/DataHandling/DataHandler.php]. The brief returned checklist items, hints for backend-typescript and javascript-unit-tests, and the changelog rule; no item constrained the wording of comments or docblocks.

## Suggestion

Add a rule, reachable from typo3_task_guide for any changeType that writes files, on the prose a patch carries — code comments, test docblocks, changelog entries. It should be prescriptive enough to act on before the text exists rather than after:

- One fact, one place. Where PHP emits a value and TypeScript consumes it, the mechanism is explained at the source and referenced, not restated.
- A comment says what the code cannot: the reason, the constraint, the trap. It does not paraphrase the statement under it.
- Plain sentences, subject-verb-object. No trailing prepositions ("the pattern it is stored by"), no clause chains, no cross-references to "above" or "below".
- Length as a ceiling, not a target: two lines for an inline comment, and a class docblock that needs more than three short paragraphs is describing a design that belongs in the changelog entry.
- The changelog entry is the place for the design argument; the comment is not.

The same rule would improve the briefs themselves. typo3_task_guide's checklist items are written in the register that produced this — "a task that writes nothing is not going to call anything", "the property is what the task produces" — and a model reading them writes comments in that voice. Making the guidance plainer is the cheapest way to make the output plainer, because a model matches what it is handed.
