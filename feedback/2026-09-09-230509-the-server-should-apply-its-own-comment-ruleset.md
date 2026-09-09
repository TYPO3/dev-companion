---
date: 2026-09-09T23:05:09+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_hint_lookup, typo3_forge_lookup, typo3_gerrit_lookup, typo3_project_describe, typo3-core-issue-triage
directory: /home/benji/projects/typo3-cms
---

# The server should apply its own comment ruleset to the prose it writes and ships

## Observation

Task: fix Forge #107495 in a core checkout. A separate feedback from this session proposes eleven rules for the comments an agent writes. This one is about where those sentences came from.

An agent writes in the register it is given. Everything I read in this session came from this server: tool descriptions, task briefs, hints, and the bundled skill files. All of it is written in the style the rules forbid. My comments were rejected twice by the user, and they were a faithful imitation of what I had been reading for the whole session.

Real lines from this session, with a plain version beside each. No plain version is longer than the original.

1. typo3_task_guide checklist: "A bugfix owes a changelog entry only where it changes what an installation renders, is configured by, or has documented."
   Plain: "A bugfix needs a changelog entry only if it changes what an installation renders, how it is configured, or what its documentation says."
   Defect: "is configured by" and "has documented" trail their prepositions and lose their object.

2. typo3_forge_lookup description: "Filed long ago is about the report, untouched for years about the attention it got."
   Plain: "The filing date describes the report. The last-touched date shows whether anyone still cares."
   Defect: two verbless clauses joined by a comma.

3. typo3_gerrit_lookup description: "It is the surface a checkout cannot see: a clone carries what landed and says nothing about what is open."
   Plain: "A clone contains merged changes only. It says nothing about changes still under review."
   Defect: "the surface a checkout cannot see" is a metaphor; the colon carries the content.

4. typo3_forge_lookup description: "A miss is an answer."
   Plain: "An empty result is a result. It means nothing matched, not that the search failed."
   Defect: a slogan that only works if the reader already knows what it means.

5. hint backend-typescript: "So an annotation is written where inference cannot reach and nowhere else."
   Plain: "Add a type annotation only where TypeScript cannot infer the type."
   Defect: passive with no actor, plus "where inference cannot reach" as a metaphor.

6. bundled skill checklist.md: "The trap: waiting to be sure."
   Plain: "Do not wait until you are certain."
   Defect: a label and a fragment instead of a sentence.

I only saw the surfaces I called, so this is a sample and not a survey.

To be fair to the source: these lines are precise, and several carry a distinction I would have missed if it had been left out. The problem is not the content. It is that the content arrives in a register an agent copies into a patch that reviewers on review.typo3.org then have to read.

## Query

Whole session against a TYPO3 core checkout: triage Forge #107495, write the patch, tests and a Breaking changelog entry, commit. Surfaces read: typo3_project_describe, typo3_task_guide, typo3_hint_lookup, typo3_forge_lookup, typo3_gerrit_lookup, typo3_changelog_lookup, typo3_rule_lookup, typo3_commit_message_guide, and the bundled skill typo3-core-issue-triage with its references/base.md and references/checklist.md.

## Suggestion

Apply the eleven rules to the server's own text, and say so in the contributing rules of this repository so later edits keep to it. The rules that bite hardest here are: name the actor, one thought per sentence, no colon as a reveal, no trailing prepositions, no invented metaphors, and say what is rather than what is not.

Two surfaces deserve different treatment.

Tool descriptions have a token budget, and short is right there. Short and convoluted are not the same thing, and the six examples above show the plain version costs no extra tokens. Where a description must stay terse, prefer a plain fragment over a compressed clause chain.

Task briefs, checklists and hints are read as instructions and copied as a model. They should be plain sentences without exception. A checklist item that has to be re-read is a checklist item that gets skipped.

A test for edits: read the line once at reading speed. If you have to go back to parse it, rewrite it. That is the same self-check the agent-facing ruleset asks for, which is the point. A server that hands out a style it does not keep teaches the style it keeps, not the one it hands out.
