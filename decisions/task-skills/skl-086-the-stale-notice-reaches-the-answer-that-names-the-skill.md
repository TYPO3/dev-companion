---
id: D-SKL-086
title: The stale notice reaches the answer that names the skill
date: 2026-08-28
status: open
coveredBy:
  - SkillTest::theBriefSaysWhichOfTheSkillsItNamesThisProjectIsBehindOn
---

# D-SKL-086 — The stale notice reaches the answer that names the skill

**`typo3_task_guide` says which of the skills it names are behind what this
server publishes. That answer is the last thing this server controls before the
client loads the file.**

The notice exists and arrives once, at initialize, before the session has any
task. A session read it and worked four of the skills it was about. Afterwards
it could not tell which of its findings were about a copy this server has moved
past.

## Evidence

- **The session.** `/home/benji/projects/bootstrap_package` on 2026-08-28,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-28-074142`](../../feedback/archive/2026-08-28-074142-the-stale-skills-notice-fires-before-the-task.md).
  It quotes the notice, names the four skills it then worked inside, and states
  the cost as one it cannot measure. Every finding it filed about skill
  behaviour has no anchor. It can name the text it read and not which release
  that text belongs to.
- **The server computes the notice once and never again.** `Server\Entrypoint`
  calls `Installer::outdated()` with the directory the process started in,
  writes the line to stderr and prefixes it to the `instructions`. Nothing else
  in `src/` reads it.
- **The brief names the skills and says nothing about them.**
  `TaskGuide::answer()` answers `skills` as a list of names. Its schema says a
  name is not a promise that the project has the skill. That is the sentence
  closest to this and it stops one step short.
- **What the check compares already exists.** `Installer::digest()` hashes the
  base and every published skill file, and the record in the project carries it.
  So the staleness is a comparison rather than a date somebody maintains.
- **A published skill says nothing about its own provenance.**
  `Installer::publishSkill()` copies the directory and writes the base into it,
  and no line of the copy names its source.

## Decided

- **The brief carries it**, because a call this server cannot see loads a skill.
  The answer that names the skill is the last moment it controls, and the
  initialize block is the first. Both stay. The one reaches a session before it
  has a task, the other one that has just got a workflow.
- **A field beside `skills` rather than objects in it.** `skills` is a list of
  strings a caller reads today, and `AGENTS.md` asks for added fields rather
  than renamed ones.
- **The digest is what a published copy carries**, where anything goes into it.
  It is what `outdated()` compares, so a session that quotes it says the one
  thing a reader can check. A date or a release number would be a second thing
  to keep true, and this package publishes no release number.
- **Queued rather than made here.** The first half is a declared schema and the
  second is what every published skill file carries. Those are the two things
  `documentation/records/judging.rst` keeps off the spot.
- **At `normal`.** One session, and the cost it reports is a report a maintainer
  cannot check rather than work that was lost.

## Assumed

- That `Instance::startedIn()` is the directory the record is in, which is what
  `Entrypoint` passes today.
- That a session given the notice on the brief acts on it, where the same
  session did not act on it at initialize. Nothing measures that, and it is what
  the first **Wrong if** watches.

## Wrong if

- A session reports the notice on a brief and works the stale skill anyway. Then
  the placement is not the lever either, and what remains is the client's, the
  same boundary `D-SKL-033` stops at.
- A project hears its skills are stale where they are not, because the digest
  moved for a reason a reader cannot see. A draft published or withdrawn is
  already one such reason.
- The version line lands and no feedback ever quotes it, which would say one
  session wanted the anchor rather than the corpus.

## Since then

Built the same day as `staleSkills`, a list beside `skills` and a line under the
one that names them. It compares the copy rather than the record.
`Installer::behind()` digests what a project has against what this package would
write there now. So a record from an older release does not decide it, and a
project this package never installed into stays silent.

This entry declines the provenance line rather than queues it, on a collision
the read found. A digest in the published base is a byte the comparison would
then have to ignore. Once ignored nothing points at the file, so only a session
that already suspects staleness would open it. The field tells that session
outright, which is the anchor the report asked for.
