---
id: D-KNW-096
title: 'How a package fills a fresh instance is a subject this server owns'
date: 2026-08-18
status: open
---

# D-KNW-096 — How a package fills a fresh instance is a subject this server owns

**How a package fills a fresh instance is a question this server answers. A
package that declares no way at all is one of the answers.**

The corpus models one mechanism, the `Initialisation/` data file and the import
the package setup fires. It states it as the shape of the answer rather than as
one of several. An extension that fills an instance some other way leaves the
step that asks with nothing to say.

## Evidence

- **The re-run reaches no intent.** This run called `typo3_task_guide` through
  `bin/typo3-dev-companion` from this worktree on 2026-08-18 with the feedback's
  own question. That is "how does this extension get content into a fresh
  instance after the installation booted". The answer is `Change type: unknown`
  with no `Recognized as:` line at all, and the hints it carries are about
  `ext_tables.php` at boot. The same guide asked with boot wording answers
  `Recognized as: Bringing an installation up and running it` and names
  `typo3-development-installation`. So the routing works and the question does
  not reach it.
- **The probe reaches the producer side alone.** `bin/cli hints:probe` for "how
  does an extension seed content into a fresh instance" and for "extension setup
  wizard backend module seeds blog root page" reaches `datahandler-seeding`. On
  the second it also reaches `backend-modules`, `browser-tests` and
  `content-elements`. `datahandler-seeding` is records written with a script,
  which is what a distribution consists of rather than how a package delivers
  one.
- **Nothing here names another mechanism.** A search of `knowledge/` and
  `skills/` for `wizard` matches `content-elements.json`, `upgrade.json`,
  `task-intents.json` and `server-scope.json`, and none of those is about how to
  fill an instance.
- **Both sides of the corpus model the one mechanism.**
  `sitepackage-initial-content` is `Initialisation/data.t3d` or
  `Initialisation/data.xml`, the site configuration beside it, and the package
  initialization event. Step 4 of `typo3-development-installation` routes to
  that hint and the two beside it. The seeding item of the
  `installation-operations` checklist is `typo3 extension:setup` and the
  inertness of `--distribution`. `D-SKL-050` sets the boundary between producer
  and consumer, and an ImpExp artifact is what both of its sides carry.
- **Two of the five calls the feedback counted are one call today.**
  `typo3_extension_describe` lists `Initialisation/data.t3d` and
  `Initialisation/data.xml` among the registration files,
  `Extension::ROOT_FILES`. It lists the `console.command` tag among the service
  tags, beside the backend modules, the site sets and where the manual is. Those
  are the two greps that returned nothing, and the session that reported says it
  should have made the call.
- **What that answer cannot carry is which module is the wizard.**
  `backendModules` are the identifiers in `Configuration/Backend/Modules.php`,
  and nothing that file declares says a module fills an instance.
- **The corpus is one session on this subject.** `bin/cli feedback:list` on
  2026-08-18 reports 27 open in two checkouts, 24 of them from
  `/home/benji/projects/blog` between 07:14 and 08:12. This is the only one of
  the 27 that asks how a package fills an instance.

## Decided

- **Step 1a, and taken on.** The gap is the knowledge that the question has more
  than one answer. Which ways a package may declare that it fills a fresh
  instance, and what holds when it declares none.
- **Where the boundary runs.** Inside: the ways a package declares it in files,
  the `Initialisation/` data file, a console command, a site set a site has to
  depend on. And the statement that a package that declares none of them fills
  the instance by a procedure only its own manual writes down. Outside: which
  backend module is the wizard, which is a guess dressed as a reading.
- **The absence is the answer, not a miss.** A caller told that nothing
  declarable is there has been told where to read next, and
  `typo3_extension_describe` already reports where the manual is. That is what
  the session that reported established by hand out of `.rst` files.
- **One card for both halves of the feedback.** The closing item it asks for,
  establish how this package expects a fresh instance to fill before the report
  of a done boot, is that knowledge delivered at the step that needs it. Its
  wording depends on what the read finds.
- **Priority `normal`, set by the counted cost rather than by arrival.** One
  session on one extension is not what raises it. Five calls with two of them
  that answer nothing is the measure `D-FBK-027` names. The gap is the corpus's
  model rather than this extension's peculiarity.
- **This entry takes the feedback on whole rather than trims it.** What is here
  today answers neither half.

## Assumed

- **That a package that fills an instance by a documented backend procedure is
  worth a statement.** One extension does it on record, and nothing here counts
  how many others do.
- **That the pointer at the manual is the lever.** The session that reported
  needed three facts and only the pointer is derivable. Nothing measured whether
  a caller sent to an extension's own `Documentation/` gets to them in one read.
- **That core declares no further way.** The three above are what the corpus and
  the extension answer already name, and the reading is what settles the list.

## Wrong if

- The reading finds core lets a package declare more ways than those three. Then
  the answer is a list rather than a list with an absence at the end of it, and
  this entry understated the gap.
- The reading finds that outside this one extension nothing fills an instance by
  a documented backend procedure. Then the statement is about one package, and a
  hint that carries it is one nobody reaches.
- A boot session that is told to call `typo3_extension_describe` at the seeding
  step establishes the same three facts without any new knowledge entry. Then
  this was step 2, the answer had arrived all along, and one line on step 4 was
  the whole of it.
- A caller given the absence answer greps the extension's manual tree by hand
  anyway. Then the gap is the structure of that manual rather than the
  mechanism, and no statement about mechanisms closes it.

## Since then

The read found more ways than the three this entry named, so its first **Wrong
if** holds in the narrow sense. The statement stands and the list was short.
Core reads four files out of a package at setup rather than one. The last of
them keys on its own hash, so it is the one shipped file that arrives again
after an edit. From the newer major a package may also listen on an event and
fill the instance with no file convention at all. The site set turned out to
fill nothing by itself.

Two things stayed as they were because neither is what the card asked for. The
extension answer reports two of the four files, and the brief still reaches no
intent for the standalone question.
