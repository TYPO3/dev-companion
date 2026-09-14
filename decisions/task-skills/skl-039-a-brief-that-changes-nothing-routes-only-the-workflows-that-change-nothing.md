---
id: D-SKL-039
title: A brief that changes nothing routes only the workflows that change nothing
date: 2026-08-14
status: open
coveredBy:
  - HintsTest::aBriefNamesTheSkillThatOwnsTheWork
  - HintsTest::aReviewOfAChangeRoutesTheReview
---

# D-SKL-039 — A brief that changes nothing routes only the workflows that change nothing

**Where `typo3_task_guide` answers a review, a triage or a boot, it names only
the task skills whose own work writes no change.**

A review request names the change it is about, and the words of that change are
the words of writing one. `breaking` is an intent with a skill behind it. So
"review core patch 95169 and say whether it is breaking" landed in the workflow
that authors a breaking change.

## Evidence

- **Measured on 2026-08-14, before the change.** "Review core patch 95169 and
  say whether it is breaking" matches `breaking` strongly and nothing else, and
  names `typo3-core-patch-development`. The German brief of the same run, "bitte
  review mir 95169 … und sag mir ob der breaking ist", matches `breaking`
  strongly and `patch-checkout` weakly. Neither reaches
  `typo3-core-patch-review`, which only the `audit` intent carries.
- **It takes both repairs, and neither alone is enough.** A withheld route needs
  a recognized review first. `audit`'s needles were "review the", "review this",
  "review of" and "reviewing", and a request that names its change by number
  arrives in none of them. The added shapes alone leave both skills named,
  `breaking` first. That is the state `D-SKL-013`'s **Since then** already ruled
  out for the `tests` intent. An assertion that the right name is among them
  holds just as well. Meanwhile a client loads a whole workflow the task has
  nothing to do with first.
- **The checklist is the counter-example to a withhold of more than the route.**
  The `breaking` intent's first item is the answer to what that brief asked.
  That is "Settle first that the change is breaking at all: `@internal` says the
  API is not public and does not decide it". Dropping the intent would take the
  answer out with the route.
- **The three negatives are this repository's own prompts.** Three prompts match
  none of the added shapes. `CORE-03`'s "Review says my commit message is
  wrong", `SKILL-13`'s "Pull down that patch from review" and `CORE-07`'s
  "pushing it for review". A bare `review` needle would have matched all three.
  The matcher ends a needle at a non-letter, so it reaches `review.typo3.org` as
  well.

## Decided

- **The property is the intent's, in data.** `changesNothing` on an entry in
  `knowledge/task-intents.json`, true for `audit`, `triage`, `patch-checkout`
  and `installation-operations`. Those are the four whose work reads a change, a
  report or an installation rather than writes one. Marking those four is the
  shorter list, and it is the same fact `TaskGuide` already forks its skeleton
  on.
- **Only the route is withheld.** The intent stays recognized, its title stays
  in `Recognized as:` and its checklist items stay in the brief. A skill is a
  workflow the caller enters and a checklist item is a statement they read. So
  what the intent knows about a breaking change still reaches the reviewer while
  the workflow that makes one does not.
- **A stated `changeType` keeps its route**, because it keeps the skeleton. That
  is `D-GUI-009`. "review the patch that deprecates X" with
  `changeType="deprecation"` is author work described from the reviewer's side.
  The fork here is the one that decision already draws.
- **`audit` gains three shapes and not the word.** `review patch`,
  `review core patch` and `review change` are how a request that names its
  change arrives. `review` on its own is what the three negatives above rule
  out.
- **The German brief stays where the corpus leaves it.** Everything below
  `knowledge/` is English and every free-text parameter says so, so what matched
  there were the two loanwords in the sentence. It routes nothing now rather
  than the wrong thing, and a translation buys its recognition as a review.

## Assumed

- **That an adjective the shapes do not carry is rare enough.**
  `review core patch` is in the list because this entry measured it.
  `review open patch` would miss, and the needle mechanism has no way to state
  "review, then a change noun".
- **That a reviewer reads an author checklist item as an obligation to check.**
  The `audit` intent already states the same obligations in the reviewer's
  voice, so the brief carries both. No run has shown whether the author-voiced
  copy costs anything.

## Wrong if

- A session hands back a changelog file, a scanner matcher or a `[!!!]` prefixed
  commit. The task asked only whether a change is breaking. Then the checklist
  does what the route did, and a withhold of the whole intent is the next step.
- A review request that names a change is still routed to a workflow that writes
  one, in a shape the three needles do not carry. Then the shapes are an
  enumeration that will not close, and the gap is a rule about the sentence
  rather than more needles.
- An author's brief loses its route because the words also read as a review.
  `remove the public method and make it a breaking change` still names
  `typo3-core-patch-development`. A task that no longer does is this decision as
  it takes the route from the caller who was in the workflow.
