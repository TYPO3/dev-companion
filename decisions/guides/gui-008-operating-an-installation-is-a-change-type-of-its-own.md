---
id: D-GUI-008
title: Operating an installation is a change type of its own
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::workThatOperatesAnInstallationIsAnsweredWithABootBrief
---

# D-GUI-008 — Operating an installation is a change type of its own

**`typo3_task_guide` has a `changeType` value for work that operates an
installation rather than changes its code. The intent it reaches is the one that
boots an environment, not the one that creates an installation.**

A session that booted a Composer project from a fresh clone got told to confirm
the target branch and keep the patch focused. Also to add test coverage and
write a commit message. That is for work that writes no file.

## Evidence

- `feedback/2026-08-03-154508`, run again on 2026-08-03 in this repository with
  the query it states.
  `task="Boot up a TYPO3 project locally for the first time from a fresh clone: install dependencies, start the local environment, import the demo database and fileadmin, build frontend assets, create a backend user, verify the site responds"`,
  `changeType="unknown"`, no area and no paths. What comes back is what the
  report says, item for item. "Confirm the target TYPO3 core branch and issue
  context", "Keep the patch focused on the stated task". "Add or update the
  narrowest useful test coverage", and the commit-message step. Those close a
  list of five conditional items that are the `typo3 setup` command's.
- One needle fired, and it is a weak one: `install`, of `installation-setup`. It
  matched the phrase `install dependencies`, which is Composer's install and not
  TYPO3's. `Text::containsWord()` matches inside a word, so the same needle also
  fires on every task text that contains `installation`. `run the installation`
  reaches it with no form of `install` alone in it.
- The five items that came back are properties of the setup command, from
  `knowledge/task-intents.json`. The password it does not print, the install
  tool password it derives from the same value. The reset path for both, and
  `--create-site` beside a sitepackage. Each holds for the creation of an
  installation and none of them for the start of one that exists.
- The corpus already carries the condition the absent value would settle. Both
  `installation-setup` and `installation-upgrade` end their `condition` with
  "rather than working on the code in one" and "rather than about the code in
  it". Those are two intents that ask the caller for a fact the enum has no way
  to state.
- `bin/cli feedback:list` on 2026-08-03: 29 open, and five of them are tasks
  that operate an installation rather than change one. Two from
  `/home/benji/projects/site-demo-typo3-org`, this one and `2026-08-03-154501`.
  Three from `/home/benji/projects/ext-guidedtour`, `2026-08-03-162826`,
  `-162836` and `-162858`, which install, seed and run a local instance and name
  `typo3_task_guide` as a tool they called.
- `D-GUI-006` is the same failure in its first shape, and its own words are what
  this one runs into. `audit` is "the one type whose rules are stated
  elsewhere", the only value on the enum that changes nothing. It asks for the
  brief a review needs. A boot is neither a review nor a patch, so `unknown` is
  the honest answer and `unknown` is the patch skeleton.

## Decided

- **A second value on the enum, and the skeleton forks on it.** `D-GUI-006`
  established that the checklist skeleton is what changes rather than what joins
  it at the end. The review skeleton it wrote is a review's. "report what the
  review did not reach" is not a step a boot takes either. So the two values
  that change nothing do not share one skeleton.
- **The word is `operations`.** It has to be safe in the matcher, because
  `TaskGuide::answer()` feeds the change type to `TaskIntents::detect()`.
  Measured against every needle in the corpus, `operations` matches none.
  `setup` and `install` are both `installation-setup`'s and would classify every
  boot as a creation. That is the failure this entry is about, in the shape the
  feedback's own suggestion would have taken.
- **The boot branch is an intent of its own**, beside `installation-setup` and
  `installation-upgrade` rather than inside either. This repository already
  separates creation from upgrade, and what it lacks is the third. The start of
  an installation that exists, from what the repository declares: its
  environment, its data import, its asset build.
- **The same commit narrows `installation-setup`'s weak `install`.** A needle
  that fires on the word `installation` reaches every task about an
  installation, and the value above is what a caller states instead.
- **The hint half of the feedback gets taken here too.** Three of the four hints
  it got — `datahandler-basics`, `fal-basics`, `public-assets` — are PHP hints,
  and `php` is the only domain the call detected. `CHANGE_TYPE_TERMS` is the
  extra domain signal a change type carries into `Domains::detect()`. So the new
  value's terms are what moves that selection, and they are part of the same
  change rather than a second card.
- **Queued rather than closed on the spot.** It adds a value to a declared
  `inputSchema` enum and forks a skeleton in `src/`.
  [judging.md](../../documentation/records/judging.rst) puts that on the far
  side of the autonomous line.
- **`normal`, and the corpus is what set it.** One session reported the shape,
  and five reported the task. Work that runs an installation reached this server
  five times on one day from two checkouts. What is not `high` about it is that
  no second session has said the brief was the wrong shape.
- **Not taken: the fallback itself.** An `unknown` that reaches the patch
  skeleton is what `D-GUI-006` decided and this entry does not reopen. What
  changes is that a caller who operates an installation has something else to
  state.

## Assumed

- The five `installation-setup` items are the creation branch's because each one
  is a property of the `typo3 setup` command. That came off the corpus file and
  the feedback's account of its run, not from a run in this repository.
- Booting an installation that exists is a third kind of work rather than the
  first half of an upgrade. `installation-upgrade`'s checklist is a migration —
  dump the database, raise PHP, run the wizards — and none of it is what a fresh
  clone needs.
- A caller who classifies work as `operations` does not also change code in the
  same call. `D-GUI-006` assumed the mirror of this for `audit`, and
  `todo/waiting/2026-08-01-115711` is that assumption under question.

## Wrong if

- A brief comes back without the patch steps for work that does change
  something. "fix the deploy hook so the import runs" is the form it would take.
  The answer is then the one `D-GUI-006`'s **Wrong if** names: the needle or the
  value carries a condition, rather than the shape goes.
- The narrow `install` needle no longer reaches a task that really does create
  an installation. `feedback/2026-08-03-162826`, an unattended TYPO3 install
  from a shell script, is the task to measure it against.
- The boot intent's checklist turns out to be Composer's and DDEV's own
  documentation restated, with nothing about TYPO3 in it. Then the gap was the
  environment's lifecycle in `typo3_project_describe`, which
  `feedback/2026-08-03-154501` reports, and not a shape in this brief.

## Since then

The value, the intent and the fork landed together, with `operations` a needle
rather than the id. The caller states what the work is, and the corpus keeps the
three installation intents named after the installation.

The terms entry could not count as decided and is empty. `CHANGE_TYPE_TERMS`
reaches the domains and with them the checks and the next lookups. The hints
match from the paths and the task text and never see the change type. So the
hint half of that feedback is open, and what it needs is a statement about the
subject rather than a domain signal. The first **Wrong if** holds and less holds
it since `D-GUI-009` ended the filter of the intent out of a call that states a
type.
