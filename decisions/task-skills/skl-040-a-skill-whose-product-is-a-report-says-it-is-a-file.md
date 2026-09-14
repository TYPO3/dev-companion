---
id: D-SKL-040
title: 'A skill whose product is a report says it is a file'
date: 2026-08-14
status: revoked
revokedBy: D-SKL-042
---

# D-SKL-040 — A skill whose product is a report says it is a file

**A skill whose product is a report says that the report is a file, what its
name is and where it goes.**

The answer then carries a short summary and that path. Three skills specify a
report exhaustively — its bands, what each finding owes, the surfaces it closes
on — and none of them says that what it specifies is a document, so the session
does the one thing nothing told it not to and prints it into the chat.

## Evidence

- `feedback/2026-08-13-214811-the-review-skill-specifies-the-order-of.md`: a
  review of Gerrit change 93319 patch set 21 under `typo3-core-patch-review`. It
  ran to a couple of hundred lines with a table and went into the chat. The user
  corrected it twice, "reviews sollten immer im markdown format ausgegeben
  werden", then "damit es kopierbar ist". The session wrote
  `review-93319-ps21.md` afterwards.
- Read again on 2026-08-14. `## Report` in
  `skills/typo3-core-patch-review/SKILL.md` fixes five severity bands and closes
  on the checklist's surfaces. `## Report` in
  `skills/typo3-extension-conformance/SKILL.md` orders findings and closes on
  coverage. *Say what the triage found* in
  `skills/typo3-core-issue-triage/SKILL.md` routes to the verdicts. None of the
  three names a form, a file or a path.
- The skill is what makes the report long. Five bands, the surface list and the
  dropped candidates cannot be short. So the skill specified the deliverable and
  left its form to whatever the session did by default.
- `skills/typo3-core-patch-review/references/checklist.md` reports a modified or
  untracked file beside the commit as a review surface. So the checkout under
  review is the one place the file may not go, and the feedback's second point
  holds as read.
- `bin/cli feedback:list` on 2026-08-14: 12 open, 10 of them out of one core
  checkout. This is the only feedback there — open or archived — that reports
  the form of a report rather than its content.

## Decided

- Queued at step 4 of the ladder. The session read and followed the section that
  specifies the report, and it does not say that what it specifies is a
  document. The fix is a rewrite of that section rather than a gap in
  `knowledge/`.
- Not closed on the spot. Three published skill bodies move, and a body is a
  copy in somebody else's project that no release of this server corrects.
- `normal` rather than `low`. One session reported it, and the correction came
  from the user twice on a deliverable this skill's own design makes long. Not
  `high`: what it cost was one answer given again.
- What the three skills say is the todo's. The report is a file, named after its
  subject and whatever separates two reports of the same one, and written
  outside the assessed checkout.
- Rejected: a statement in `knowledge/`. No caller asks for it, and it is a rule
  about the form of the skills here.

## Assumed

- All three of those workflows end in a document somebody carries elsewhere. A
  reader reads which skills those are off the bodies rather than off a field, as
  with the sides a description names.
- The session can write a file at all. Where a client cannot, the report stays
  in the answer, and the path is the caller's to name in any case.

## Wrong if

- A review under the rewritten skill still prints its report into the chat. Then
  the gap was behaviour rather than wording and the rung was the wrong one.
- A user asks for the report in the answer rather than at a path, which would
  make summary-plus-path the cost instead of the fix.
- One of the three turns out to produce something short enough that a file is
  ceremony. A triage of a single issue is the candidate.

## Revoked on 2026-08-14

Asked the same day this entry came in, the maintainer answered that the report
may stay in the chat. What it has to be is copyable, and formatted HTML is what
a reader cannot transfer. The second **Wrong if** above is the one that held,
and it held within the hour. So the statement names one way to be copyable as
the requirement, and `D-SKL-042` carries the property instead.

What this entry read stands. The three sections specify a report exhaustively
and name no form, and `D-SKL-042` builds on that read.
