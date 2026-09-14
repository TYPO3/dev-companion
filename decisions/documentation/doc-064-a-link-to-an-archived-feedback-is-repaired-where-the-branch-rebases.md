---
id: D-DOC-064
title: 'A link to an archived feedback is repaired where the branch rebases'
date: 2026-08-27
status: open
coveredBy:
  - LinksTest::aLinkToAFeedbackThatWasAnsweredIsRewrittenToTheArchive
  - TodoHomeTest::theArchiveRepairRunsOnTheRebasedTreeAndBeforeTheSuite
---

# D-DOC-064 — A link to an archived feedback is repaired where the branch rebases

**A dead link whose target is a feedback the archive now holds gets the archive
path. `bin/cli links:repair` writes it, and so does `todo:home` on the rebased
tree.**

`feedback:archive` runs before the link that breaks exists, so the move cannot
carry its own repair. The rebase is the first moment one tree holds both.

## Evidence

- Three branches failed `todo:home` on 2026-08-27 — `todo/T-260825-6e96`,
  `todo/T-260826-1572` and `todo/T-260826-4194`. Each archived a feedback, and
  each rebased onto a `main` that had gained a decision that names that feedback
  where it had stood.
- `LinksTest` was what failed, and `bin/cli links:check` named the file and the
  line every time. The session that could have fixed it had ended before the
  branch's rebase.
- The repair was the same on all three: the path gained `archive/` and nothing
  else.
- Only `decisions/` cites a feedback report by path, and on this entry's day it
  did so 58 times across six groups. Fifty-two of those already point into
  `feedback/archive/`, and the other six name a report that is still open. Each
  of them is a link that breaks when somebody answers it.

## Decided

- **The archive is where a link to an answered feedback points after the
  repair**, in the file that wrote it. A rewrite rather than a report, because
  the target stands known and no read of the file tells one answer from another.
- A dead link the archive does not explain stays what it was — a failure with
  nothing this repository can do about it.
- `links:check` names the repair beside the link. So a checkout that met the
  class some other way has the command rather than a diff to work out.
- `todo:home` runs it beside `todo:park` and the two index commands, after the
  rebase and before `composer ci`, amended onto the branch's own commit.
- What that amend stages is now the whole tree rather than three named
  directories. The tree read clean before the rebase, so everything dirty after
  those commands is what they wrote. A repair reaches whatever file holds the
  link.
- Rejected: a resolution of the link through a read of the archive in
  `Links::dead()`. The check would pass and the link would still take a reader
  nowhere, which is the defect rather than the report of it.
- Rejected: the citation at its archive path from the start. A citation names a
  feedback while it is open, and that path does not exist yet.

## Assumed

- That `feedback:archive` is the only move this repository makes to a file its
  own prose names by path. Every other rename goes through `decisions:rename`,
  `requirements:rename` or `decisions:renumber`, which rewrite the references in
  the same pass.
- That a claim comes from `main` and rebases onto it, which is what `todo:home`
  already assumes, `D-DOC-063`.

## Wrong if

- A repair rewrites a link that pointed at an open feedback of the same name as
  an archived one. That would need two feedback with one file name.
- `todo:home` amends a file the branch had no business with. Something other
  than those four commands wrote into the tree between the rebase and the
  status.
- A dead link that is not a feedback turns out to have one right repair too.
  Then the command's name is for a class it no longer only serves.
