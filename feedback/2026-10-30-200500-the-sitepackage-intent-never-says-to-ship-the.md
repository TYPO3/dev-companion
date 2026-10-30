---
date: 2026-10-30T20:05:00+00:00
category: missing-knowledge
status: open
model: claude-fable-5
tool: typo3_task_guide, typo3_hint_lookup
directory: /home/lina/trials/site12-with
---

# the sitepackage intent never says to ship the data migration it causes

## Observation

A run of 2026-10-30 built a sitepackage over an installation that already
renders. It declared three backend layouts of its own, then moved the seven
existing pages onto them: `pagets__CaminoStartpage` became `pagets__startpage`,
and the two others likewise. It wrote the change through DataHandler rather
than SQL, which this server told it to do.

**The package it shipped holds no PHP at all.** There is no `Classes/`, no
`Configuration/Services.yaml`, no `ext_localconf.php`. The session ran the
migration as a script and left nothing behind, not even the script.

So the data moved in one installation. A second installation of the same
package keeps `pagets__CaminoStartpage` on its pages, and the set no longer
defines that identifier, because the session also removed the theme it came
from. Those pages fall back to the default template. Nothing fails, and the
start page loses its layout without a word.

## The statement exists and the session never saw it

`upgrade-wizards` in `knowledge/hints/upgrade.json` answers this in full. It
gives `UpgradeWizardInterface`, the `#[UpgradeWizard]` attribute, what
`updateNecessary()` answers about, and why a clean `upgrade:run` is not
evidence that a wizard ran. Its `appliesTo` already names "data migration" and
"migrate records".

The words `upgradewizard`, `upgrade:run` and `UpgradeWizardInterface` appear
nowhere in the run.

## Why the route misses

The `sitepackage` intent is titled "Starting or laying out a sitepackage", and
its checklist is written for a site that has nothing in it yet. Twelve steps
name a dozen hint ids, from `sitepackage-start` to `sitepackage-focus-contrast`.
None is `upgrade-wizards`, and no step mentions content that is already there.

The intent's own match list carries "design into typo3" and "build this site in
typo3". Both reach a session whose installation is full. The checklist then
says nothing about what the session owes the rows it touches.

## What makes this hard to see

The control run of the same task, with no companion, changed no page and needs
no wizard. Its package is correct as it stands. So the gap opens only where the
session does the better thing, and a comparison of the two packages reports the
one that migrated as the one with a defect.

## Suggestion

Judge whether the `sitepackage` intent takes a step for the installation that
is not empty. The step routes to `upgrade-wizards`, and it states the rule
rather than the mechanism: a change to editor data that the package depends on
travels with the package.

Weigh that against a condition on the step, because a greenfield sitepackage
owes nothing here and the checklist is already twelve steps long.
