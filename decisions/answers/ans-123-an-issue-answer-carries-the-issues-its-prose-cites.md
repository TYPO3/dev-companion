---
id: D-ANS-123
title: An issue answer carries the issues its prose cites
date: 2026-08-27
status: open
coveredBy:
  - ForgeTest::aReviewChangeTheReportNamesIsAHandleTheSameWay
  - ForgeTest::anIssueWithNoRelationsStillSaysWhatItsProseCites
  - ForgeTest::theIssuesAnIssuesProseCitesAreLiftedOutOfIt
---

# D-ANS-123 — An issue answer carries the issues its prose cites

**`typo3_forge_lookup` lifts the issues a description or a note cites into a
field beside `relations`, whether they stand as `#NNNN` or as a URL.**

A reporter's "this is the 6.2 bug still in 7.6" is the claim a patch stands
against. It sits in the first line of a description while the structured answer
says `relations: []`.

## Evidence

- `feedback/2026-08-26-223231`, a session that shipped a patch for #76202. Its
  description opens with
  `seems bug from 6.2 version https://forge.typo3.org/issues/62553 is still in typo3 7.6 version`.
  The tool answered `relations: []`, and the session read that as nothing
  linked. Issue #62553 is a different defect, closed in 2014, so the report's
  frame was wrong. The session was one prompt from a repeat of it in a commit
  message.
- Re-run on 2026-08-27 through `bin/typo3-dev-companion`. Issue #76202 answers
  `relations: []` still, the URL still stands in the first line of
  `description`, and nothing in the payload names it. The report reproduces as a
  property of the answer.
- The URL form the report asks for is the rare one. The descriptions of the 100
  oldest and the 100 newest open core bugs, read against forge.typo3.org the
  same day. 5 carry a `forge.typo3.org/issues/NNN` URL at all. That is 8 URLs,
  of which 7 are already relations. A repair that lifted URLs alone would have
  caught one row in 200, and that row is the one the report came from.
- Redmine's own citation syntax is where the citations are. The same 200
  descriptions carry 29 bare `#NNNN` candidates, 27 of which resolve to an issue
  on the tracker. **No relation carries 15 of the 27.** 11 of those 15 point at
  an issue the tracker has closed, resolved or rejected, which is the prior-art
  claim this entry is about.
- The two that resolve to nothing come from one row and are three digits long,
  which is what the resolution read filters. TYPO3's exception codes — two of
  them in #76202's own description — are ten digits and are outside the bound
  either way.
- The notes carry the same thing more thinly. Of 30 oldest open core bugs read
  whole the same day, 2 carry a review URL in a note. 6 carry a bare `#NNNN`
  somewhere in their text. 3 of those numbers are in no relation.
- The report's premise about the review server is half wrong, and the other half
  is a second gap. `Forge::issueOf()` builds `reviews` from the journal alone,
  so a review URL in a description drops out. 5 of the 100 newest open bugs
  carry one, and they name 6 changes that no answer names today.
- What it costs is nothing on most calls. `Forge::issue()` already reads the
  related issues in one bulk request through `fields()`, so the citations
  resolve in the call the answer makes anyway. One read joins only on an issue
  that cites something and has no relations at all, which is the shape #76202
  has.

## Decided

- Built as `mentioned` on the issue path, beside `relations`. It is the issues
  the description and the notes cite, each with subject, tracker, status and
  URL. So a caller weighs a claim about prior art without an issue read per
  number.
- The field lifts both forms, and keeps a number only where the bulk read
  resolves it. `#NNNN` runs from three to six digits, which is every issue the
  tracker holds and no exception code.
- `where` says `description` or `note`, and a citation in both is a
  `description`. A relation is somebody's triage and a citation is the writer's
  own claim, which is why the two stay separate fields rather than one list.
- A number `relations` already carries stays out of `mentioned`. The relation
  says more about it, and 12 of the 27 citations measured are that.
- `reviews` comes from the description as well as from the journal. One pass
  over the same two texts, and it closes the gap the report's premise assumed
  was already closed.
- The text half says it where `relations` is empty. `relations: []` under a
  description that cites an issue is the sentence this feedback is about.
- **The enumeration rows stay out.** A row is what a caller chooses a candidate
  on, and it carries no description to hold the citation against. What makes a
  citation worth anything is the sentence around it, which a caller reads on the
  issue path. `relations` already answers the linked half there.
- Queued rather than made in the judgement run. The fields are `src/` and a
  declared output schema, which is the half `D-FBK-052` does not relax.

## Assumed

- That a resolvable number in prose is a citation. A four-digit version or line
  number that happens to be an issue on the tracker would come back as one.
  Nothing here reads the sentence around it.
- That the reporter's claim is worth its place even where it is wrong. Issue
  #62553 was the wrong prior art, and the field exists to find that out. A
  `mentioned` entry never says the two issues are the same defect.
- That the citation rate measured on open bugs holds for the issues a triage
  reads. Both samples are open bugs at the two ends of the backlog, which is the
  population `open` hands over.
- One session, one issue reported.

## Wrong if

- A session reads a `mentioned` entry as a duplicate and passes an issue over,
  which is the reporter's claim taken for triage. That is the failure this entry
  describes, in the other direction.
- The field comes back with numbers nobody cited: a version, a line, a count
  that resolved. Then the bound is the sentence rather than the digits, and the
  URL form lifted alone was the safe half.
- A sweep of the backlog reports `mentioned` empty throughout. Then the
  citations sit on the issues nobody enumerates, and 15 in 200 was a property of
  the two ends rather than of the corpus.
- A triage reports that it lacked the citations on the enumeration rows, which
  would say the boundary above sits one call too late.
- Redmine starts to answer its own citations as relations, which would make the
  parse unnecessary rather than wrong.
