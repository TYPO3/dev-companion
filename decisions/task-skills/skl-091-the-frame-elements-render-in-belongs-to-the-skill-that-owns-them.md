---
id: D-SKL-091
title: 'The frame elements render in belongs to the skill that owns them'
date: 2026-09-04
status: open
---

# D-SKL-091 — The frame elements render in belongs to the skill that owns them

**`typo3-content-element-development` owns the shared object every element
renders inside, and its description opens on both that and the single element.**

A session rebuilding that frame read the opening clause *build or refactor TYPO3
content elements* as "not this" and never opened the skill. The body agreed with
it: it says where to put one element's rendering and nothing about what that
rendering resolves into.

## Evidence

- `feedback/2026-09-04-053744`. `typo3_task_guide` named two skills and neither
  was invoked. The session says what shut this one: the description is framed
  around *a* content element, and its task was the frame every element on the
  site shares — a `lib.contentElement` definition, a layout, a header partial,
  a `Generic` template and one core element's rendering definition.
- The reading holds against the body. Its rendering items are *put one element's
  rendering in a dedicated TypoScript file* and *keep the Fluid template under
  the project's content-element template root*, and its closing line is *this
  skill owns content-element architecture and implementation*. The frame is in
  none of it, so widening the description alone would have promised a section
  that is not there.
- The failure shape is on record twice already. `D-SKL-061` is the clause a
  description opens on narrowing everything after it, and `D-SKL-076` is a body
  owning two jobs while the description names one. This is the first of those,
  measured on a third skill.
- The other half of the session's report is a strength and is read as a
  boundary: `typo3-extension-health` was ruled out in one read, on *the audit
  reports first; nothing is changed before the list is agreed*, against a user
  who had said not to ask. A description precise enough to be declined
  correctly is doing its whole job — `D-FBK-018`.

## Decided

- **The frame is this skill's domain and not a new one.** The maintainer settled
  it on 2026-09-04 against a skill of its own and against a contract case alone.
  A skill that says where an element's rendering goes and is silent about what
  it goes into has a hole in its own domain; a new skill would have cost a
  baseline run and a permanent contract in every project on one session's
  evidence.
- **An edit rather than a new file, so no baseline run is bought** — `D-SKL-035`
  charges that to a new skill alone.
- The section is routing and method. What a removal actually owes stays in
  `sitepackage-fluid-styled-content`, which the section names by id, so the
  skill keeps no second copy of what a tool owns.
- `typo3_record_lookup` joins the skill's routings, between the rule lookup and
  the commit message guide, because the inventory step reads what the site's
  content carries rather than what its templates allow.
- The description names the frame without naming the extension that usually
  supplies it. The listing budget is 360 characters and the trigger words the
  session actually used — dependency, drops, supplying — carry it without a
  package name a release of this server could not correct.

## Assumed

- That a session reading the new opening clause places a frame task inside it.
  Nothing measured that; the two entries above are the same inference on two
  other skills, each confirmed by a later session.
- That the four bullets are the order the work really goes in. They are the
  reporting session's own account of what it did, reordered so the inventory
  comes before the edit.

## Wrong if

- A session doing single-element work now reads the frame section as owed and
  spends the inventory step on a task that changes one template.
- A second session reports the same shut door, which would mean the opening
  clause is still read as the single element.
- The frame turns out to need the steps a removal owes in the body rather than
  behind a hint id, because a session holding the id does not call it.
