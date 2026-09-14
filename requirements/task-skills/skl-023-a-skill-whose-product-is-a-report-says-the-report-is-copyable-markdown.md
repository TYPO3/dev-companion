---
id: R-SKL-023
title: 'A skill whose product is a report says the report is copyable markdown'
status: held
restsOn: [D-SKL-042]
heldBy:
  - SkillTest::aReportIsCopyableMarkdownAndTheAnswerIsWhereItGoes
---

# R-SKL-023 — A skill whose product is a report says the report is copyable markdown

**A skill whose product is a report says that the report is markdown the reader
can copy, and that the answer is where it goes.**

The report exists to move, into a change, an issue, a ticket or a chat, and
rendered output is what does not survive that. The skill is also what makes the
report long. A section that specifies severity bands, what each finding owes and
the surfaces it closes on cannot produce four lines. Length is why the form
matters at all.

A path is what the caller asks for rather than what the skill prescribes, so
nothing has to decide a file name or a directory. Where the caller asks for one,
it is outside the checkout the skill assessed. The same workflow either reports
on that working tree or leaves it as it found it.

Which skills produce a report comes off the bodies rather than off a field, like
the sides a description names.

## From

`feedback/2026-08-13-214811`: a review of Gerrit change 93319 patch set 21 under
`typo3-core-patch-review`. It ran to a couple of hundred lines with a table and
went into the chat. The section that specifies the report never said that what
it specifies is a document. The user corrected it twice: "reviews sollten immer
im markdown format ausgegeben werden", then "damit es kopierbar ist".

The second sentence is the demand, and the first read took it one step too far.
`D-SKL-040` made the file the session wrote afterwards the requirement. Asked on
2026-08-14, the maintainer answered that the report may stay in the chat and
needs no path. Formatted HTML is what cannot move. That is `D-SKL-042` and the
revocation of the entry before it.
