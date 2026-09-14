---
id: D-SKL-042
title: A report is copyable markdown, and the answer is where it goes
date: 2026-08-14
status: open
---

# D-SKL-042 — A report is copyable markdown, and the answer is where it goes

**A skill whose product is a report says that the report is markdown the reader
can copy, and the answer is where it goes.**

A file is one way to be copyable and not the property the maintainer asks for.
What the report has to survive is a move somewhere else, a Gerrit comment, an
issue, a chat. Rich rendering is what it does not survive.

## Evidence

- The maintainer, asked on 2026-08-14 in the session that merged `D-SKL-040`,
  said the report may go in the chat and needs no path. "but the user must be
  able to copy it from there, the problem with formatted html is that is cannot
  be easily transferred".
- The correction that produced the feedback says the same thing, and `D-SKL-040`
  read it one step too far. "reviews sollten immer im markdown format ausgegeben
  werden", then "damit es kopierbar ist". The second names the reason, and
  markdown is the fix it asks for. `D-SKL-040` read the file the session wrote
  afterwards as the requirement rather than as one way to meet it.
- What that entry established holds. The three `## Report` sections specify five
  bands, what each finding owes and the surfaces it closes on, and none of them
  names a form.
- The skill is what makes the report long, and length is why the form matters. A
  hundred lines rendered as HTML is the case where a copy breaks; four lines is
  not.

## Decided

- The property is copyable markdown, stated in the report section of
  `typo3-core-patch-review`, `typo3-extension-conformance` and
  `typo3-core-issue-triage`.
- The answer is where the report goes by default. Nothing goes to a path unless
  the caller asks for one. So nothing has to decide a name or a directory, and
  no accident dirties the assessed checkout.
- A file stays available and is the caller's call. Where a session writes one,
  it goes outside the checkout under review. That skill's own checklist reports
  an untracked file beside the patch as a finding.
- This entry revokes `D-SKL-040` rather than corrects it. Its statement named
  the fix as the file. Its **Wrong if** were a list about paths and names, which
  is not what can go wrong here.

## Assumed

- A client that renders markdown still hands the source over when the reader
  copies it. Where one does not, the form is not what fixes this and a path is
  back on the table.
- The three skills are the ones whose product is a report. Read off the bodies
  rather than off a field, as `D-SKL-040` assumed before it.

## Wrong if

- A review under the rewritten skill still arrives in a form the reader cannot
  lift out. Then the sentence names a form the session does not control, and
  where the report goes is the lever after all.
- A user asks for the report at a path often enough that "the answer, unless you
  ask" is the wrong default.
- A report turns out to be long enough that nobody copies it at all. That would
  make the deliverable the thing to shorten rather than the form to fix.

## Since then

All four skills whose product is a report carry the sentence, and a test holds
the set rather than one file.
`SkillTest::aReportIsCopyableMarkdownAndTheAnswerIsWhereItGoes` reads
`typo3-core-patch-review`, `typo3-extension-health`, `typo3-core-issue-triage`
and `typo3-extension-patch-review` for both halves. Those are the form, and what
makes it the form, "rendered output is what does not survive being moved".
`typo3-extension-conformance`, which the reporting feedback named beside triage,
is inside `typo3-extension-health` since `8a67338a`.

None of the three **Wrong if** has a report behind it. Nothing since 2026-08-14
describes a review in a form the reader could not lift out. Nothing describes a
user who asks for the report at a path, or a report nobody copied because of its
length.
