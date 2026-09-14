---
id: D-SKL-048
title: A build workflow says a symptom is a lookup trigger
date: 2026-08-18
status: open
coveredBy:
  - SkillTest::theBuildWorkflowSaysASymptomIsALookupTrigger
---

# D-SKL-048 — A build workflow says a symptom is a lookup trigger

**The content-element workflow says a symptom is a query `typo3_hint_lookup`
takes, and the installation workflow does not, because that is where the
measurement stopped.**

Both workflows route their lookups by subject and at plan time, which is the one
moment a subject is what the session has. Debugging is where it does not.

## Evidence

- `feedback/2026-08-17-205945`, a v14 demo site on 14.3.6. It fetched twenty-one
  hints by id during the plan and consulted the index at no point after the
  first symptom. That was nine debug cycles and roughly 45 round trips, the
  largest cost item of the session. Four of those had an answer in the corpus.
  In three cases the id stood in an `availableHints` array in the same context
  window.
- [`D-SKL-045`](skl-045-a-build-workflow-names-the-guide-at-the-step-that-needs-it.md)
  left this half undecided and said why. A sentence that sent a caller with a
  symptom to the index sent it to a miss. The domain gate dropped the hint that
  explained the failure. That gate closed the same week.
  [`D-ANS-084`](../answers/ans-084-a-curated-phrase-crosses-the-domain-gate.md)
  is the rule, and
  [`R-ANS-031`](../../requirements/answers/ans-031-a-symptom-reaches-the-hint-that-explains-it.md)
  is what must hold from now on.
- The two probes that entry named, re-run on 2026-08-18. "the content elements
  render in reverse order" now returns `datahandler-placement`. "f:asset.css
  does not appear in the rendered page" still misses `fluid-layouts-sections`.
  That is curation rather than the gate, and it is in hand on its own branch.
- A wider sweep with `bin/cli hints:probe` the same day. The two feedbacks name
  four content-element symptoms that have an answer in the corpus at all, and
  three reach it. Those are the reverse order, the child rows that saved
  unlinked, and the backend preview that shows nothing. The last returns
  `content-element-preview` first on a phrase that borrows none of its words.
  The asset case is the fourth. The absent partial argument is a subject the
  corpus does not carry and has a card of its own.
- Installation symptoms reach in six of eleven, and the five that do not are the
  ones a session actually stops on. A 500 after the install returns
  `frontend-access-restriction`, a blanket not-found returns `extbase-arguments`
  first, a container that started green while the install failed matches
  nothing. What those failures are about is the container, Composer and the
  console, and the corpus is about TYPO3.

## Decided

- **The line goes into `typo3-content-element-development` and nowhere else.**
  It is a section of its own between the implementation and the verification. A
  place under `Establish evidence` returns it to plan time, which is the moment
  that already worked.
- **The example is the shape of the query, not the id that answers it.** An id
  in a published skill is a curation fact no release of this server corrects.
  The need to know the id is the work the caller did not do.
- **The installation workflow is not touched.** Half its symptoms reach a hint
  from another subsystem. A promise of more than the matcher keeps costs the
  caller the call the line exists to produce (`D-ANS-081`).
- **What the workflow adds to the base is the moment, not the tool.** Step 4 of
  [`base.md`](../../skills/base.md) already fixes `typo3_hint_lookup` with the
  paths in scope. This says the same tool takes the observation, and that the
  call comes before the read the base fixes as the step after the lookups.
- **No requirement.** What one would state is every build workflow, and the
  measurement above says the opposite.

## Assumed

- That a session reads the section at the moment it breaks. A client loads a
  skill whole and nothing brings a session back to a section, which is
  [`R-SKL-024`](../../requirements/task-skills/skl-024-a-build-step-a-guide-answers-names-the-call-that-fetches-it.md)'s
  own open assumption one workflow over.
- That the content-element symptoms measured are the shape a session arrives
  with. They are this feedback's own, written down after the session ended.

## Wrong if

- A content-element session makes the call and gets an answer out of a layer it
  never asked about. Then the gate widened too far and the line sends a symptom
  to noise, which is `D-ANS-084`'s second **Wrong if** by way of this entry.
- An installation session reports a symptom answered cheaply by the same call.
  Then the sweep above sampled the wrong queries and the line belongs in that
  workflow too.
- A session reads the section and reads the installed source first anyway. Then
  the surface is not the lever, and no wording at it will be.

## Since then

An installation session tested the second **Wrong if** and it did not fire. The
symptom it ends on reaches nothing when probed as that session would phrase it.
It reaches two hints on its subject, neither of which states the thing. So the
decision to leave that workflow holds for its own reason. The changelog at the
major in play answers what an installation session stops on. A line that sent
the symptom to the hint index would have sent this one to a miss. What the
session reports instead is that nothing invited the question once the workflow
ended, which is `D-SKL-049`'s closing form.
