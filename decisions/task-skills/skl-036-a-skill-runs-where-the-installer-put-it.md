---
id: D-SKL-036
title: A skill runs where the installer put it
date: 2026-08-12
status: open
coveredBy:
  - SkillTest::theReadmeSaysAHandCopiedSkillIsUnsupported
---

# D-SKL-036 — A skill runs where the installer put it

**This package does not support a copy of `skills/` taken out of this repository
by hand, and `readme.md` says so.**

Publication is what completes a skill. The installer writes `references/base.md`
into each directory, and a copy that never went through it opens on a link to
nothing.

## Evidence

- Every `SKILL.md` carries a `compatibility` line naming the server it needs and
  how it is installed, so a copy says what is missing. It does not put the base
  there.
- The installer writes `Installer::BASE` at publication, one copy per skill.
  Each lands in another project alone, and a link out of its own directory would
  resolve here and nowhere a session reads it.
- A copy that works means one committed `references/base.md` per skill, which
  two things refuse. `digest()` hashes `skills/base.md` once because it is the
  only copy here. `publishSkill()` writes the copy itself, so every publication
  overwrites a committed one and it is stale in between.
- Nobody has reported a copy of the directory. This entry priced the three
  options against each other on 2026-08-08 and against no session that hit it.

## Decided

- Unsupported, and said in `readme.md` where somebody about to copy the
  directory looks, rather than in a skill they would only read afterwards.
- Rejected: a committed base per skill. It buys an unreported case with a file
  in the repository that is stale between publications and overwritten at each
  one.
- Rejected: a louder failure of the copy. The dead link and the `compatibility`
  line already say it, and anything further is a mechanism for a case nobody has
  hit.

## Assumed

- That somebody who copies a skill directory reads `readme.md` first, or reads
  it once the link fails.

## Wrong if

- A session uses a copied skill against this server and never notices the absent
  base. So the order every task starts in is silently absent.
- A client appears that reads a skills tree straight out of a git checkout. That
  would make the copy the ordinary way in rather than a mistake.
