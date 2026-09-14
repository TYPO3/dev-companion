---
id: D-FBK-023
title: A correction is judged by what its withdrawal moves
date: 2026-08-02
status: confirmed
---

# D-FBK-023 — A correction is judged by what its withdrawal moves

**A feedback that corrects earlier ones gets its judgement as a test of each of
their judgements against the premise it withdraws. It gets no walk down the
ladder on its own.**

Only a judgement that used the withdrawn claim as evidence moves. The ladder is
still owed once, for the correction's own lever, and that lever is rarely the
subject the earlier notes were about.

## Evidence

- `feedback/2026-08-01-003736` withdraws one claim from three notes of the same
  session: that the `typo3-extension-testing` skill "was never activated" and
  that "no skill was activated". Its ground is that the conversation it reports
  from begins at an anchored summary. So the skill activation, if there was one,
  is before its window. It keeps the rest: the score of the skill's workflow
  "remains valid as far as it goes".
- `bin/cli hints:probe` on its `Query` matches nothing and returns 22 hints as
  the index. That is not step 1a. The subject is what one session could see of
  itself, which is neither TYPO3 nor anything `knowledge/` would hold.
- [`documentation/records/judging.rst`](../../documentation/records/judging.rst)
  does not assess whether a self-criticism is accurate. So a withdrawal can only
  move a judgement that took the claim as evidence rather than as the report it
  came in. Three siblings, three answers.
- `002926` — archived on 2026-08-02. Its judgement is the **Since then** of
  [`D-AUD-003`](../audience/aud-003-the-instructions-carry-the-entry-point-because-the-tool-descriptions-never-arrive.md)
  and
  [`R-SKL-010`](../../requirements/task-skills/skl-010-a-skills-description-names-every-side-of-what-it-owns.md),
  and it rests on the descriptions themselves.
  `typo3-content-element-development` opened on "frontend content elements" and
  reached `previews` ninth of eleven. The task was a backend preview the same
  skill covers in as many words. That came off the files here, and
  `SkillTest::aBackendPreviewTaskMatchesTheSkillThatOwnsTheElement` holds the
  rewrite. The withdrawal moves the sentence about what the session called, not
  the result, and it stands in that entry.
- `003533` — judged as
  [`D-KNW-017`](../knowledge/knw-017-a-verification-question-is-routed-to-the-layer-that-verifies-it.md),
  step 3, on four hint probes against this repository. That entry already
  records the withdrawal and says the trigger is not its lever. Unmoved, and its
  todo stands.
- `003634`, in hand on `todo/self-rating-against-the-typo3-extension-testing` on
  this entry's day, so nothing of it changed here. What the withdrawal does to
  it is the largest of the three. The reason it gives for all five of its
  section scores is "skill never activated via the skill tool", and that is no
  longer evidence. If the skill was active, its Playwright half is a rule that
  arrived and did not take, which is rung 4 rather than rung 3.
- The lever is this repository's own debrief prompt, in
  [asking-for-a-debrief.rst](../../documentation/records/asking-for-a-debrief.rst).
  It asks for exactly the fact the session could not see, "Report the session
  you just had from your own transcript". It offers the skill question two
  answers: name it, or "If none activated, say so — that is a result". An agent
  whose context begins at a compaction summary has no transcript for the first
  half of its own run. Neither answer is the one it could give.
- The other four bullets ask for the same kind of fact: which calls, in which
  order, how many round trips each cost. So the qualification belongs on the
  lead sentence and not on the skill bullet.
- This entry established nothing about TYPO3. Every fact above is this
  repository on 2026-08-02.

## Decided

- Rung 4, wording, and closed on the spot. The prompt's lead sentence now asks a
  session whose transcript begins at a summary to say so and answer for the part
  it can see. The rule was already there, "not from how it felt". The gap is
  what the transcript half is worth when there is not one.
- The map above is the judgement of the correction and stands here so nobody
  derives it a second time. No todo follows it. One sibling is in the archive,
  one has its judgement with its todo in the queue, and the third gets its
  judgement elsewhere.
- Rejected as the placement: `typo3_feedback_record`'s parameter descriptions.
  They would also reach a session that files without the prompt. But they are a
  declared schema, which `judging.md` queues rather than improvises, and one
  incident is not the evidence for a contract change.
- What must hold from now on is
  [`R-FBK-012`](../../requirements/feedback/fbk-012-a-debrief-reports-the-window-the-session-could-see.md);
  this commit archives the feedback.

## Assumed

- That the window was what the correction says it was. Nobody here has the
  transcript, which is the point. This repository can check neither the claim
  nor its withdrawal, and the change does not depend on which is right.
- That a sentence in the prompt changes what an agent reports. Nothing has
  measured it, and the feedback filed after 2026-08-02 is where it would show.
- That the three notes are one session. The correction says so and the dates are
  minutes apart, but `002926` reports a different task from the other two.

## Wrong if

- A feedback filed after this reports a skill as never activated from a window
  that began at a summary, and does not say so. The prompt is then not the
  lever, and the tool's parameters are the next placement.
- The added sentence buys a hedge on everything, and debriefs arrive unable to
  say what happened in their own session. "If none activated, say so — that is a
  result" is what that costs. `D-AUD-003` and `R-SKL-010` both came from a
  report of exactly that shape.
- A sibling's judgement turns out to have rested on the withdrawn claim after
  all. `003634` is the open one.

## Confirmed on 2026-08-23

The third **Wrong if** has its answer and it is the one this entry left open.
`003634` got its judgement on 2026-08-02 by the map `D-FBK-021` sets. The
premise the correction withdrew is a row of that map rather than something the
judgement rested on. The row reads *the premise that the skill was never
activated — withdrawn by `003736`*. `D-KNW-017` records the withdrawal and reads
the trigger out. Both feedback are archived.

Nothing reports the other two. No feedback since names a skill as never
activated from a window that began at a summary. None arrives hedged about what
happened in its own session. `documentation/records/asking-for-a-debrief.rst`
still asks for the skill that never activated and the tool it passed over as
evidence. That is the sentence the second **Wrong if** prices.
