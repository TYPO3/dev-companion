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

**`Installer::skills()` is the whole of `skills/`. So the commit that writes a
workflow's directory publishes it, and there is no draft status and no
`--drafts`.**

`D-SKL-027` gave a skill a way to hold itself back until somebody had reviewed
it. No file has declared itself one since 2026-08-19. The installer, the
entrypoint, the record, the digest and three tests carried the mechanism the
whole time.

## Evidence

- Nothing in `skills/` declares the status. The two that did went out on
  2026-08-19, per `D-SKL-054`'s **Since then**, and none has come since.
- Its removal takes 182 lines out of `Installer` and `Entrypoint` and six
  members with them. Those are DRAFT, DRAFT_KEY, drafts(), draft(), declaring()
  and publishSet(), plus the `drafts` key of the record and the set the digest
  folded in.
- The smoke test that held it asserted nothing. It opened with the draft list,
  which is empty. So both of its loops over the published copies ran zero times
  and what remained was two reads of an absent key.
- The review the mechanism served is a step of
  `documentation/contributing/writing-a-skill.rst`. A reviewer carries it out
  with a read of the file, which no flag delivers and nothing here holds either
  way.

## Decided

- The installer publishes every directory below `skills/` with a `SKILL.md`.
  That is the one place that writes the set, as it has been since `D-SKL-021`
  took the list out of the class.
- This entry revokes `D-SKL-027` and is its `revokedBy`. What survives it is the
  closed front-matter field set, which it decided in the same move.
  `SkillTest::everyFrontMatterFieldIsOneTheStandardDefines` still holds that,
  and that attribute moves here.
- The digest drops the set it folded in, because there is one publication per
  release again rather than two.
- The record drops `drafts`. The installer reads past a state file that still
  carries the key rather than migrates it.
- The sentences in `D-DIS-013`, `D-DIS-021`, `D-SKL-021` and `D-SKL-054` that
  name the mechanism keep their finding and gain a pointer here. `R-DIS-025` and
  the three documentation pages state what holds and get their correction in
  place.

## Assumed

- That a reviewer can review a workflow where its author writes it. A read of a
  skill in this repository is not a read where it loads, which is the argument
  `D-SKL-027` rested on. What answers it now is that the next release corrects a
  skill which does not work rather than holds it back from one.
- That no project holds a copy published under `--drafts` before 2026-08-19.

## Wrong if

- A half-finished workflow reaches somebody's project, because the review step
  is now the only thing between the commit and the copy and nothing holds it.
- A skill needs a trial where it loads before anybody will publish it. The
  branch it sits on is one no project can install from. Then the flag comes back
  as a per-run choice over a named directory rather than as a field in the file.
- A project that published a draft before 2026-08-19 still has the directory.
  Nothing lists it any more, so no `update` removes it and a deletion by hand is
  the repair.
