---
id: D-ANS-081
title: A symptom is answered across the domain it was observed in
date: 2026-08-18
status: revoked
revokedBy: D-ANS-084
coveredBy: []
---

# D-ANS-081 — A symptom is answered across the domain it was observed in

**A symptom names the layer a failure showed in. The domain gate keeps the hint
that explains it out of the answer, by the domain that hint lives in.**

The second index the feedback asks for is already there. The search covers a
hint's own statements beside its curated vocabulary, and the symptom is the
language of those statements. The domain gate withholds the answer, and it faces
the same question the caller did: where does this belong. In a plan the two
agree, because the words of a task name the layer the work is in. Debugging is
where they part.

## Evidence

- `feedback/2026-08-17-212010`, re-run on 2026-08-18 with `bin/cli hints:probe`.
  Its first query, "inline children are created but uid_foreign stays 0",
  returns `datahandler-relations` first on `appliesTo(11) + text(613)`. That is
  the id the feedback names, reached largely through the hint's own prose. Its
  second, "f:asset.css does not appear in the rendered page", returns
  `css-source-build-boundaries` and `public-assets` and not
  `fluid-layouts-sections`.
- The axis exists.
  [`R-KNW-021`](../../requirements/knowledge/knw-021-a-hint-is-reachable-by-what-it-says.md)
  scores a hint's statements, and `Hints::FIELD_WEIGHTS` weighs `title` and
  `appliesTo` at 4 against `text` at 1. Two of the hints the feedback names
  carry the symptom as curated vocabulary as well. `datahandler-placement` lists
  "reverse order", "reversed order" and "wrong order", and
  `datahandler-relations` lists "relation not saved" and "children not linked".
- A third symptom from the same session shows what withholds them. "the content
  elements render in reverse order" returns `content-elements` and
  `content-element-shape`. `datahandler-placement`, which carries the query's
  own words, neither comes back nor stands in the index beside it. "content
  element" is a Fluid and TypoScript keyword in `Domains::KEYWORDS`, and nothing
  in the sentence is a PHP one. `Hints::find()` builds its candidates from the
  selected domains alone. The same sentence with "in the backend" appended
  selects PHP and returns `datahandler-placement` second.
- `availableHints` does not repair it. The refused set it comes from is the same
  domain-gated candidate list, so a hint the gate dropped does not appear as an
  id either.
- The miss on `fluid-layouts-sections` is the other cause and is lexical rather
  than gated. The gate selected Fluid, and the query carries none of the hint's
  words. "a viewhelper call outside a section is never executed" reaches it on
  its prose alone, `text only(339)`.
- Nothing tells a caller that a symptom is a query `task` takes. The parameter
  reads "Short task description or topic". The `routing` block names this tool
  for "Working in a concrete file and unsure about the subsystem's conventions".
  The session says it never tried the call it describes.
- `feedback/2026-08-17-205945` is the same session on the same moment from the
  other side — which lookups it made after the first exception. Its card is in
  hand on another branch and reads the moment as routing. This entry reads what
  the matcher does with the query when the call comes.

## Decided

- Not a second index. The corpus gains no "what it looks like when this goes
  wrong" field. The search already covers the field it would duplicate, and that
  answers where the symptom carries the mechanism's words.
- The gate is the gap, and it goes to the queue as a measurement before a
  change. An exact `appliesTo` phrase is what a curator wrote for the query that
  should reach the hint. So one let past the gate is the smallest candidate. The
  sweep that holds `R-KNW-021` says whether it widens into an answer to
  everything.
- The `task` parameter says a symptom is a query it takes, in the words the
  measurement supports, and lands with it rather than before it. Promising a
  caller more than the matcher keeps costs the trust that made the call.
- Rejected: a higher `text` weight. That is the knob `UNDILUTED_WORDS` and
  `MAX_MEAN_BODY_WORDS` exist to keep still, and it would answer a symptom out
  of whichever hint is longest.

## Assumed

- That the gate is right everywhere else. Its measurement covered task
  descriptions, where the words of the query and the layer of the work are the
  same thing. This entry claims only that a debug session separates them.
- That the four cases the session reports are one shape. This entry checked
  three and the gate held two of them; the fourth is a subject the corpus does
  not carry.

## Wrong if

- The measurement shows the gate pays for itself on symptoms too. The sweep
  loses recall, or hints from layers the query never meant come back once a
  curated phrase gets past.
- A session reports the opposite failure, a symptom answered from a layer it did
  not ask about, after the gate widens.
- The next symptom reported as a miss turns out lexical like
  `fluid-layouts-sections` rather than gated. That would make this a curation
  task and not a matcher one.

## Confirmed on 2026-08-18

The measurement this entry queued ran over 199 queries. Those are the twelve of
the sweep and the recorded misses beside them, every hint title, and every
forward and contract prompt. It compared what each returned before and after.
The first **Wrong if** did not hold: the sweep keeps its recall and its two
negative controls, and no answer lost an entry. The second one did for both of
the wider rules on trial, which is why what shipped is neither of them.

The third one holds for the query it covers. "f:asset.css does not appear in the
rendered page" still misses `fluid-layouts-sections`. The gate selects Fluid,
the hint is inside it, and the query carries none of its words. That half of the
feedback is a curation task, and it is what remains of the feedback.

## Revoked on 2026-08-18

By the change this entry asked for. The statement is what the matcher did until
that afternoon, and the gate no longer keeps out the hint that explains the
symptom. So a reader who stops after the bold sentence would take it for a
description of today. The evidence stays, because the shape of the change came
off it, and `D-ANS-084` holds from here.
