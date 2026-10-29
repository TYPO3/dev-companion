---
id: D-ANS-193
title: A feature task is asked for the extension that already does it
date: 2026-10-29
status: open
coveredBy: []
---

# D-ANS-193 — A feature task is asked for the extension that already does it

**Before the first file, a feature brief asks for the published extension that
already covers the feature, and weighs it.**
`typo3_ter_lookup` takes a subject beside a key, so the caller can find that
extension rather than recall its name.

One session built its own record table, content element and data processor for
dated news items on a 14.3 site. Afterwards somebody asked about
`georgringer/news`. `typo3_ter_lookup` answered in one call that a current
release supports 13 and 14. Nothing in the brief had raised the question —
`feedback/2026-10-28-133242-nothing-asks-whether-an-extension-already-does.md`.

## Evidence

- The re-run of 2026-10-29. `bin/cli hints:probe` with the query of the
  feedback reaches `plugin-template-override` alone. That hint overrides the
  templates of a plugin somebody already chose. The probe with the words of the
  task reaches nothing and returns the index.
- No intent in `knowledge/task-intents.json` matches the words of the task.
  `content-element` matches on "content element", "CType" and "tt_content", and
  the task names none of them.
- `TaskGuide::CHANGE_TYPE_CHECKLIST` gives `feature` two items: a core changelog
  file and functional tests. Neither asks what already exists.
- The corpus weighs a third-party extension at one moment only, the upgrade. The
  `upgrade` hints and the `installation-upgrade` checklist say an extension
  without a release for the target major decides the schedule. That is the
  counterweight the feedback asks for, and it exists today.
- `typo3_ter_lookup` takes an extension key and nothing else. Its description
  already says that `majors` and `constraint` are what the release declared.
- `feedback/archive/2026-08-31-234323-nine-of-eighteen-tools-never-called-and-three.md`
  is a second session that looked for a package by subject. It assumed the TER
  tool could not answer and ran `composer search` instead. That is the same
  discovery shape from another task.

## Decided

- The judgement is *taken on* (`documentation/records/judging.rst`). The
  question is step 1a, because no rule asks it. The discovery is step 1b,
  because no tool answers "what exists for this subject".
- It is not closed on the spot. The line that reaches every feature task stands
  in `src/Tool/TaskGuide.php`, and the discovery is a parameter on a declared
  schema. Both get a review.
- The line goes into the `feature` checklist and points at a hint. The hint
  states both weights. A published extension brings a documented model and an
  upgrade path other sites share. It also couples the site's upgrade to its
  release schedule, and its unused surface is still surface a maintainer reads.
- The discovery is a live read of the registry, not a list in `knowledge/` of
  extensions per subject. Such a list is recall about a moving ecosystem with
  the confidence of a lookup, which is the failure the feedback names.
- The search is a parameter of `typo3_ter_lookup` rather than a new tool. A
  query goes in and the matching entries come out, which is the `lookup` shape.
- Priority `normal`. One session reported it. The cost is front-loaded, though,
  and it rises with every record and every shared URL after the first file.

## Assumed

- That extensions.typo3.org offers a search a client can read without a
  credential. Nobody here has checked, and it is the first step of the todo.
- That a feature task reaches the `feature` checklist. A caller who describes
  the work and passes no `changeType` does not get that checklist.

## Wrong if

- The registry offers no search a client can read. The discovery half is then
  *proposed*, and the maintainer decides whether a bundled list is worth what it
  costs to keep true.
- A session builds a feature without the line in its brief because it passed no
  `changeType`. Then the checklist is the wrong owner, and `skills/base.md` or
  the task text match is where the question belongs.
- A session receives the line, names no extension, and builds its own anyway.
  That is step 4 of the ladder, a rewrite of the line.
- A session takes an extension the site uses a fraction of, and a later report
  names what that cost. Then the counterweight in the hint is too weak.
