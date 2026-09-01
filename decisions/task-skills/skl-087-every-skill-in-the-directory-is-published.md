---
id: D-SKL-087
title: Every skill in the directory is published
date: 2026-09-01
status: open
coveredBy:
  - SkillTest::everyFrontMatterFieldIsOneTheStandardDefines
  - SkillTest::everySkillInTheDirectoryIsPublished
---

# D-SKL-087 — Every skill in the directory is published

**`Installer::skills()` is the whole of `skills/`, so a workflow is published by
the commit that writes its directory and there is no draft status and no
`--drafts`.**

`D-SKL-027` gave a skill a way to hold itself back until somebody had reviewed
it. No file has declared itself one since 2026-08-19, and the mechanism was
carried in the installer, the entrypoint, the record, the digest and three tests
the whole time.

## Evidence

- Nothing in `skills/` declares the status. The two that did were published on
  2026-08-19 — `D-SKL-054`'s **Since then** — and none has been written since.
- Removing it takes 182 lines out of `Installer` and `Entrypoint` and six
  members with them: DRAFT, DRAFT_KEY, drafts(), draft(), declaring() and
  publishSet(), plus the `drafts` key of the record and the set the digest
  folded in.
- The smoke test that held it asserted nothing. It opened with the draft list,
  which is empty, so both of its loops over the published copies ran zero times
  and what was left was two reads of an absent key.
- The review the mechanism served is a step of
  `documentation/contributing/writing-a-skill.rst` and is carried out by reading
  the file, which no flag delivers and nothing here holds either way.

## Decided

- A directory below `skills/` carrying a `SKILL.md` is published. That is the
  one place the set is written, as it has been since `D-SKL-021` took the list
  out of the class.
- `D-SKL-027` is revoked and this is its `revokedBy`. What survives it is the
  closed front-matter field set, which it decided in the same move and which
  `SkillTest::everyFrontMatterFieldIsOneTheStandardDefines` still holds — that
  attribute moves here.
- The digest drops the set it folded in, because there is one publication per
  release again rather than two.
- The record drops `drafts`. A state file that still carries the key is read
  past rather than migrated.
- The sentences in `D-DIS-013`, `D-DIS-021`, `D-SKL-021` and `D-SKL-054` that
  name the mechanism keep their finding and gain a pointer here; `R-DIS-025` and
  the three documentation pages state what holds and are corrected in place.

## Assumed

- That a workflow can be reviewed where it is written. Reading a skill in this
  repository is not reading it where it loads, which is the argument `D-SKL-027`
  was written on, and what answers it now is that a skill which does not work is
  corrected in the next release rather than held back from one.
- That no project holds a copy published under `--drafts` before 2026-08-19.

## Wrong if

- A half-finished workflow reaches somebody's project, because the review step
  is now the only thing between the commit and the copy and nothing holds it.
- A skill has to be tried where it loads before anybody will publish it, and the
  branch it sits on is one no project can install from. Then the flag comes back
  as a per-run choice over a named directory rather than as a field in the file.
- A project that published a draft before 2026-08-19 still has the directory.
  Nothing lists it any more, so no `update` removes it and deleting it by hand
  is the repair.
