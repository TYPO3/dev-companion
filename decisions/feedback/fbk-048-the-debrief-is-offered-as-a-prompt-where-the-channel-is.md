---
id: D-FBK-048
title: The debrief is offered as a prompt where the channel is
date: 2026-08-18
status: open
coveredBy:
  - StdioServerTest::theDebriefIsAvailableAsAPromptTakingNoArguments
---

# D-FBK-048 — The debrief is offered as a prompt where the channel is

**The server offers the debrief as a user-invoked prompt wherever the feedback
channel is available, and never as a tool the model can call.**

Everything this server learns about itself arrives through a text somebody
pastes by hand. A session that is not handed it files nothing, and the store
cannot tell that session from one that had nothing to report.

## Evidence

- `feedback/2026-08-18-071603` reports the mechanism from inside it. At the
  moment its task finished the session had filed nothing and intended to file
  nothing. Every one of its ten results came afterwards from a question. Five of
  the questions that produced them are bullets of the prompt in
  `documentation/records/asking-for-a-debrief.rst`, and the feedback names them
  one by one.
- 30 of the 405 archived feedback name a debrief in their own text, and the 27
  open ones come from two directories. Both are sessions run against a
  standalone checkout, which is the only configuration where
  `Channel::isAvailable()` offers the two channel tools at all.
- `Factory::create()` already ships one prompt, `commit_message`, built from
  `typo3_commit_message_guide`'s own answer.
  `documentation/usage/installing.rst` and `knowledge/server-scope.json` both
  call it *the user-invoked prompt*, so the surface exists and this repository
  already has the word for what it is.
- `asking-for-a-debrief.rst` states the constraint the shape has to satisfy. The
  debrief comes in a message of its own after the work. An agent that expects
  the question about which tools helped calls tools to have an answer.

## Decided

- Taken on, at step 1b of the ladder: the answer exists and there is no way to
  get it in the form the task needs. The gap is not knowledge but a surface. The
  questions exist, and only a person with the page open can put them.
- The prompt registers where `Channel::isAvailable()` is true, beside the two
  tools it ends in. A session that cannot record a feedback has no use for the
  questions.
- It takes no arguments. The list is generic on purpose: it names no scenario,
  no skill and no tool. A parameter would be the first thing to make it name
  one.
- One text, read by the page and by the prompt. A second copy ages, and the copy
  that ages is the one somebody pastes.
- Rejected: typo3_debrief_guide, which is the shape the feedback asked for. A
  tool stands in the model's list from the first call, so the session under
  report learns a debrief will come while it still works. That is the
  contamination the page opens with. The id the feedback wants is the prompt's
  name.
- Rejected: the workflow skills that end with its name, the way they name
  `typo3_commit_message_guide`. A session reads a skill body at the start of a
  task, so a last step arrives at step 0 and carries the same contamination. A
  skill is also installed into somebody else's project, where the channel it
  would point at is not offered.
- The sample bias the feedback names is an account of the corpus rather than a
  rule. So it goes to `documentation/records/judging.rst`, where a session reads
  the corpus before it walks the ladder.
- What the prompt *says* is not decided here. `D-FBK-047` owns the list of
  questions, and two clauses the feedback earned joined it in the same commit as
  this entry.

## Assumed

- That a prompt is user-controlled: listed for the person, not offered to the
  model. That is what the protocol says a prompt is for, and what this
  repository already writes about `commit_message`. A client that renders
  prompts into a model's context carries exactly the contamination that rejected
  the tool.
- That the client under debrief lists prompts at all. Where it does not, the
  page and the paste stay the only way, which is why the page stays.
- That a prompt run by the person after the work reproduces what the paste
  produces. Nothing has measured it; the prompt is the same text by a shorter
  route.

## Wrong if

- Feedback starts to arrive that names the debrief as something the session knew
  would come. Calls made to have an answer, a result written before the task
  finished. That is the tool's failure on the prompt.
- The prompt's text and the page's drift apart, and what somebody pastes is a
  version behind what the server offers.
- The prompt ships and the corpus still arrives in the same hand-pasted batches,
  which would mean the surface was never the obstacle.
