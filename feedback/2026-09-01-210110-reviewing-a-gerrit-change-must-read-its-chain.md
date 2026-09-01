---
date: 2026-09-01T21:01:10+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3-core-patch-review, typo3-core-patch-checkout, typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# Reviewing a Gerrit change must read its chain before calling structure a defect

## Observation

Task was to review and rebase core change 94652, which adds two backend services and is the bottom of a two-change chain. The change is not merged, so the names below are left out and only the shapes are given.

The review reported four structural findings that were all wrong, and the reason is a step neither the review skill nor the audit brief asks for:

- a subnamespace below View/ holding one helper while the renderer sits flat beside it — "pick one"
- #[Autoconfigure(public: true)] on a resolver that is only ever constructor-injected
- the renderer left non-final while its sibling is final
- the renderer's $GLOBALS accessors protected while the sibling's are private

Each is defensible read against the change alone. All four are refuted by change 94653, which sits on top of it in the same chain: it fills the one-class subnamespace with five more classes and rewrites both the renderer and Services.yaml. The one-class subnamespace, the non-final class and the public service were groundwork, not oversights.

typo3_gerrit_lookup already returns this — the `chain` key was in the very first answer of the session, listing 94653 with thisChange:false — but nothing in the audit brief or the review skill says that a change stacked on top is evidence about the shape of the one under review, so it was read as "there is a follow-up" and not consulted again. The author's correction was "du änderst viel das ggf bewusst gesetzt worden ist" ("you are changing a lot that may have been set deliberately").

Same reasoning decided a second question later in the session: whether to add tests. A functional test pinning the renderer's markup is worthless when the next change in the chain replaces that markup, while a test for the resolver — absent from 94653's file list — would survive. The chain answers "what is this change's structure for" and "what will still be here afterwards", and both belong in a review.

## Query

Task: "review and rebase change 94652". Called typo3_gerrit_lookup with change="94652", messages="people", then typo3_task_guide with changeType="audit" and the paths the change touches, then reviewed the diff.

## Suggestion

Add a step to the audit/review brief and to typo3-core-patch-review: when the change under review is part of a chain, read the changes stacked on top of it before judging its structure, its API surface or its test coverage.

Concretely, the brief should say:
- typo3_gerrit_lookup's `chain` key names them; a change with thisChange:false above the reviewed one is a follow-up whose file list is evidence.
- A namespace holding one class, a class left non-final, a service declared public with no visible caller, a seam that looks unused — these are the shapes a follow-up explains. Report them as questions to the author, never as defects, until the chain has been read.
- The follow-up's file list also decides what a test is worth: a test pinning behaviour the next change rewrites is churn, one covering a class the follow-up does not touch is durable.
- Where the reviewed change is the bottom of a chain, say so in the report, because rebasing it obliges a rebase of everything above it.

The checkout skill has the mirror of this: it already tells the session to carry the change number and target branch across to the development workflow, but not the chain, so the "94653 now needs rebasing too" consequence has to be rediscovered.
