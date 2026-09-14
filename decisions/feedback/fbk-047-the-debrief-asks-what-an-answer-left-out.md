---
id: D-FBK-047
title: 'The debrief asks what an answer left out'
date: 2026-08-18
status: open
---

# D-FBK-047 — The debrief asks what an answer left out

**The debrief prompt asks what an answer stopped short of, what the session
wished for and what the run cost. That is beside the calls it already asked
about.**

A session reports what it did, and what it did is call tools. A list weighted
towards the surface comes back with the surface, and the corpus the task
actually turned on gets no mention.

## Evidence

- Of the ten bullets the prompt carried, six asked about the surface: skills
  twice, calls, resources, names, errors. One asked what the session had to
  establish elsewhere. That one ended "that this server should have answered".
  That hands the scope judgement to a session that has read `doesNotCover` and
  will drop exactly the results that say the boundary is wrong.
- The 29 open feedback on 2026-08-18: 17 `idea`, 6 `missing-knowledge`, 5
  `tool-gap`, 1 `bug`. The 404 in the archive: 140 `missing-knowledge`, 122
  `idea`, 64 `tool-gap`, 43 `bug`, 35 `wrong-answer`.
- No bullet asked whether a statement held on the TYPO3 version the session
  worked against, though `since` and `until` are what the corpus binds with.
- What a session costs has its measure in `D-FBK-020` from this repository's own
  worktree sessions. For a session that uses the server as a caller, the ones
  this prompt goes to, there is no figure at all.

## Decided

- Two new bullets. The answer that was right and stopped one step short, which
  is what the corpus fails at rather than absence. And what the session would
  have wanted, asked without the scope test it would otherwise apply.
- Three widened. The round-trip bullet asks which single call would have settled
  a question several went into. The name bullet extends from a tool's name to
  what it answered. Knowledge under a term nobody tried reads as knowledge that
  is not there. The error bullet names a statement that did not hold on the
  version in front of the session.
- The scope clause is gone from "what you had to establish elsewhere": all of
  it, sorted here rather than there.
- The cost question stands at the end, after the results, and reaches no file.
  It is information for whoever pasted the prompt.
- Rejected: a cost field on `typo3_feedback_record`. One figure establishes
  nothing, the series is what would, and the series is the maintainer's job
  rather than a schema's.
- Rejected: a merge of the two skill bullets to hold the list at ten. The
  counterfactual half is what the page defends, and no reader asked for a
  shorter list.
- The prompt moved out of `documentation/records/readme.rst` into a page of its
  own, so what a person pastes has a place to point at.

## Assumed

- A client reports what a session cost, or the session says that it does not.
  Nothing here can check either.
- Twelve bullets are still answered to the end. The one that catches a list read
  half way is the last, which asks what the list did not.

## Wrong if

- The next twenty feedback are as thin on the corpus as the last twenty.
  `missing-knowledge` and `wrong-answer` together stay under a quarter of what
  arrives.
- The wish bullet comes back with wishes the server already answers. That would
  mean the session dropped its knowledge of the tools along with the scope test.
- The answers thin out towards the end of the list, or stop before it.

## Since then

The first measure of this list from inside a session names which question
produced which result. Five of the twelve bullets produced one, so the list
works where somebody already read it.

Two things it names fold into bullets rather than join as new ones, because what
this entry rejected was a longer list. The round-trip bullet now asks for the
walk and says what memory drops. A session that walked its own calls again found
five where it remembered two. And the bullet about an answer that stopped one
step short now carries a second read of the answers. The session read past a
strong-confidence answer while it was on the screen.

What the feedback also shows is the boundary of a written list. Three results
stayed on the floor until the user asked three plain questions of their own.
