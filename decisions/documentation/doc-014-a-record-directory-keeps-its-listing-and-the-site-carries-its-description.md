---
id: D-DOC-014
title: A record directory keeps its listing and the site carries its description
date: 2026-08-03
status: open
---

# D-DOC-014 — A record directory keeps its listing and the site carries its description

**`requirements/` and `decisions/` are work directories. Each readme carries
what a command writes into it and a line that points at the page that describes
the entry.**

The description stood in two places at once. Both readmes opened with an
explanation of what an entry is. The page that already existed on how to write
one explained it again a directory away.

## Evidence

- The split it replaces was never decided. `documentation/readme.rst` stated it:
  the directories say what a thing is and link away for how a session carries it
  out. `D-DOC-004` applied it once and cut `requirements/readme.md` from 107
  lines to 62. No entry in `decisions/` carried it.
- The duplication was in the same words. `requirements/readme.md` and
  `writing-a-requirement.md` both said that **open** is the backlog. Both said
  that a requirement nobody has implemented and one that could silently regress
  are the same kind of thing.
- What remained in the readmes past that start was the group table, the
  three-digit width and the never-reused rule. All three are properties of an
  entry rather than of the directory it sits in. The choice of a group is part
  of how to write one.

## Decided

- The whole description moves to `documentation/requirements/` and
  `documentation/decisions/`, the group tables with it. What stays is the
  generated listing and a pointer.
- `decisions/readme.md` keeps its `## Every decision, newest first` heading,
  because `bin/cli decisions:index` writes the listing under it.
- `todo/` and `scenarios/` are not changed here. They still describe themselves,
  which is the older convention, and a move of them is a separate sweep.

## Assumed

- That a reader who lands in one of the two directories follows one link rather
  than expects the description where it used to be. Nothing measures which of
  the two entry points is actually taken.

## Wrong if

- Somebody files an entry under the wrong group, or with a number of the wrong
  width. They wrote in the directory and never opened the page. The checks catch
  the width; the group is a judgement nothing fails on.
- A session that writes into a record directory this repository does not have
  yet has to guess which of the two conventions applies. `todo/` and
  `scenarios/` still hold their own description.
- Nobody reads the page whole any more, because it now carries where an entry
  goes and how to write it at once.

## Since then

That last one fired, on 2026-08-04 and one day after this entry. Each kind now
has two pages rather than one. What it is, with its states and what it stands
on, beside where an entry goes and how to write it. The statement above stands:
both pages are documentation, and the work directories still hold only what a
command writes into them.
