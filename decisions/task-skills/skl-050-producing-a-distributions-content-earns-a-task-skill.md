---
id: D-SKL-050
title: "Producing a distribution's content earns a task skill"
date: 2026-08-18
status: confirmed
---

# D-SKL-050 — Producing a distribution's content earns a task skill

**The production of the content artifact a distribution ships earns a task skill
of its own.** It owns the seed, the export, and where the artifact and its files
go in the package. The site configuration ships beside the export rather than
inside it, and a clean install is what proves the whole. The project repository
half of the same report needs nothing built: `typo3-development-installation`
sequences it in step 5 and did not arrive.

The feedback asks for two skills and names the second one's territory
accurately. The first one's is already written, in the skill the session
activated by hand.

## Evidence

- **The re-run reproduces the routing.** This entry called `typo3_task_guide`
  through `bin/typo3-dev-companion` from this worktree on 2026-08-18, with the
  feedback's own query and `changeType: feature`. The answer opens
  `Recognized as: Adding or changing a content element` and names one skill,
  `typo3-content-element-development`. `installation-setup` is not named at all,
  weakly or otherwise, though the brief's first unit is a development
  installation.
- **No skill names the producer side.** A search of `skills/` for
  `impexp-artifact`, `datahandler-seeding`, `impexp` and `export` returns
  nothing. The five hints the feedback assembled its sequence from are
  `sitepackage-initial-content`, `initial-content-import-once`,
  `initial-content-references` and `impexp-artifact` in
  `knowledge/hints/distribution.json`, and `datahandler-seeding` in
  `knowledge/hints/datahandler.json`.
- **The installation skill owns the consumer side and stops there.** Its step 4
  seeds the content a developer works a package against and routes to three of
  those hints. Those are which command imports the file, why a changed one does
  not arrive a second time, and what the import remaps. It names neither of the
  two hints that write the file in the first place.
- **The project half has an owner by name.** Step 5 of the same skill is "Decide
  what the install wrote into the repository". It routes to
  `project-configuration-files` and `project-build-and-scripts`. It closes: "The
  ignore rules follow from both answers and are written before the first commit,
  not after the first accidental one." That is the feedback's first gap,
  sequenced, in a published file.
- **The rest of that gap has owners too.** `typo3-extension-testing`'s
  description names Playwright, PHPUnit, PHPStan and php-cs-fixer.
  `typo3-extension-upgrade` owns "proving every version it claims", which is the
  untested PHP floor. `typo3-development-installation`'s own **Where this
  stops** names the container declaration.
- **The session's next report contradicts this one's count.**
  `feedback/2026-08-17-213027`, filed three minutes later by the same session,
  says seven of the user's ten findings sit inside
  `typo3-extension-conformance`'s stated scope. It names that scope: TCA,
  content elements, site sets, TypoScript, Fluid, labels, icons. This feedback's
  seven are the ignore file, Playwright, PHPUnit, php-cs-fixer, editorconfig, a
  composer script, a seeding script, the PHP floor and the container PHP
  version. The two lists share nothing, so at most one of them is seven of ten.
  Nothing here can check either, because the ten findings are the user's and sit
  in no file this repository holds.
- **Another session reached the producer side on its own three weeks earlier.**
  `feedback/archive/2026-07-29-180809`, from `/home/benji/projects/site-new`: "I
  regenerated Initialisation/data.xml three times in one session and never once
  imported it ... I verified the artifact by reading the XML and checking that a
  softref pointed where I expected. That is reasoning, not verification." That
  is the terminal proof this feedback says nothing owns, missed from another
  project on another task.
- **The order exists only as neighbour sentences.**
  `sitepackage-initial-content` closes by naming the other three hints and what
  each one adds. `feedback/2026-08-17-211306`, archived, is the same session's
  report that a session reads such a closing sentence where the appetite for
  another lookup is lowest.
- **The corpus is one session on this half.** `bin/cli feedback:list` on
  2026-08-18 reports 13 open, all in `/home/benji/projects/site-demo`, all
  `claude-opus-5`, all recorded between 20:59 and 21:30 on 2026-08-17. The
  second arrival is the archived report above rather than a second open card.

## Decided

- **Step 1b for the distribution artifact, and taken on.** The answers are here
  and nothing says in which order to ask for them, which is the half of that
  rung a skill fills.
- **Step 2 for the project repository, and it belongs to another card.** The
  rule is here, in the skill the session activated by hand, and it did not
  arrive. What failed to deliver it is `2026-08-17-213027`'s subject — a
  crossing named in a closing sentence — and that card is already in the queue.
- **One skill rather than the two the feedback proposes.** The other skills pay
  for a description and not its own (`D-SKL-026`), so a thirteenth costs the
  twelve. The second one would buy a workflow step 5 already carries.
- **Where the boundary runs.** Inside: the seed of the content with DataHandler
  because nothing exists to export yet, and the export with the table and
  relation flags it takes. Also where the artifact and its files directory go in
  the package, and the site configuration shipped through
  `Initialisation/Site/`. Also the clean install that proves the result.
  Outside, unchanged: what the import does on the installation that receives it,
  which is the installation skill's step 4.
- **The compound brief is its own card.** The guide named one skill for a brief
  naming three units, and `installation-setup` matched none of it. That is step
  3 on an intent and a skill that both exist. A check of it needs no read about
  TYPO3, and the new skill does not improve it.
- **Priority `normal` on both cards, and what sets it is arrival rather than
  weight.** Two sessions in two projects reached the distribution gap, which is
  not `low`. It is not `high` either. `D-SKL-035` buys a new skill a baseline
  run, and `impexp-artifact` carried two wrong claims into this very session,
  corrected the same day by `D-KNW-080`. So the skill would route to answers
  under repair last night.
- **The feedback shrinks rather than goes to the archive.** Its project half has
  its answer above and goes. The distribution half stays open behind the card
  that builds the skill.

## Assumed

- **That the project half is delivery rather than ownership.** Step 5 sequences
  the two project hints. Whether a session that reaches it writes the ignore
  rules from those answers instead of from memory stands open here. This session
  never reached the skill through routing at all.
- **That the production and the consumption of a distribution are two
  workflows.** Read as one, the work is four steps added to the installation
  skill's step 4 and no new description is spent.
- **That the later of the two counts is the correct one.** Both come from one
  session thirty minutes apart, and nothing here can read the ten findings they
  divide.

## Wrong if

- The read finds the producer side is four hints in an order and one verify
  command. Then it belongs on the installation skill's step 4, and this entry
  built a skill for a checklist.
- `2026-08-17-213027`'s card lands, a session reaches
  `typo3-development-installation` from the guide, and the ignore rules are
  still written before the install has revealed what it generates. Then the
  project half was 1b after all and step 5 is not enough.
- The baseline run `D-SKL-035` buys shows a session without the skill seeding,
  exporting, placing and proving in that order anyway. Then the order was never
  the gap.
- The thirteenth description pushes a skill out of the listing every client
  reads under one budget, and the one dropped is
  `typo3-development-installation`. Then this entry bought one workflow by a
  quiet removal of the one that owns the other half of the same task.

## Confirmed on 2026-08-18

The producer side ran end to end on two installations and the first **Wrong if**
did not fire. Four of the steps carry a failure that reports success, and this
session paid for three rather than read them. A page takes the hidden default
its TCA declares. So the first seed shipped three hidden pages and the
installation that received them answered 404 on all of them. DataHandler splits
a NEW id on its last underscore and reads it as a table name. So a relation
wrote three rows with no parent and logged nothing. The site configuration route
copies the whole directory, so the yaml shipped alone produced a site that
resolved and answered 500.

What the read could not settle is what publication owes. The published
descriptions stand three characters under the ceiling, which is the fourth
**Wrong if** as a wall rather than as a displaced skill.
