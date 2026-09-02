---
id: D-SKL-051
title: A site built from scratch reaches the installation intent
date: 2026-08-18
status: open
coveredBy:
  - KnowledgeTest::aBriefNamingOneKindOfWorkConfirmsThatKindAndNoOther
  - KnowledgeTest::aChangeThatEndsInABackendUiIsNamedTheBrowserCheck
  - KnowledgeTest::everyKindOfWorkHasSuchABrief
  - SkillTest::aBriefThatNamesSeveralUnitsRoutesToTheSkillOfEach
---

# D-SKL-051 — A site built from scratch reaches the installation intent

**`installation-setup` is reached by the words a brief names the installation
with, and not only by the verb that sets one up.** A site asked for from scratch
matches it, and the installation named as a unit of the work matches it weakly.
Nothing in `src/` changed: a brief carries as many intents as it recognizes and
always did.

The six strong needles were five verb phrases and `fresh installation`, so a
compound brief whose first unit is a development installation matched none of
them.

## Evidence

- **The re-run reproduces `D-SKL-050`'s.** `typo3_task_guide` with the
  feedback's own query and `changeType: feature` answered `scope: extension`,
  one intent — `content-element`, strong — and one skill,
  `typo3-content-element-development`. `installation-setup` was not named at
  all.
- **One intent was never all the answer can carry.** `TaskGuide::answer()`
  returns every detected intent and `TaskIntents::skills()` collects the skill
  of each confirmed one. Measured on the same day, the brief written for
  `labels` was recognized as `labels` and `backend-ui` together, both strong.
- **The scope reading is not what withheld the route.** `installation-setup`
  declares no `scope`, so `TaskIntents::scoped()` leaves it as it matched, and
  `skills()` answers a brief outside the core with the intent's `skill` —
  `typo3-development-installation`. What `scope: extension` withholds is
  `skillCore`, which this intent does not have.
- **The matcher was not in the way either.** `Text::containsWord()` matches a
  needle's inflections and reads a compound written with a space or a hyphen, so
  `development installation` in the brief would have matched a needle of that
  name. There was none.
- **The widening moved one line in 61.** One brief per intent — nineteen — and
  every prompt in `scenarios/`, forty-two of them, were matched before and
  after. The only difference is the compound brief, which now answers
  `installation-setup` strong beside `content-element` and routes to both
  skills.
- **The same miss is in a contract case.** `SITE-01`'s prompt opens "Fresh TYPO3
  here. Set up the site" and matches no installation intent either. It is left
  as it is: that case states the installation steps as not covered, and it was
  written before `typo3-development-installation` was published.

## Decided

- **`site from scratch` matches strongly.** A site built from scratch has no
  installation yet, so making one is the first unit of that work rather than a
  possibility to be qualified.
- **`development installation` matches weakly.** The adjective says which
  installation, not that one has to be made — "fix the routing bug in my
  development installation" names where the work is. That is what separates it
  from `fresh installation`, which is strong and says the installation does not
  exist yet.
- **Two needles rather than a general one.** `from scratch` on its own reaches
  an extension and a content element written from scratch, which is the
  neighbour-swallowing the measurement above is for.
- **The third unit of the brief stays unrouted.** Producing a distribution's
  content has no intent, because it has no skill yet — that is `D-SKL-050`'s
  card, and this entry does not anticipate its words.

## Assumed

- **That a site asked for from scratch always begins with an installation to
  make.** Nothing measured says how often the phrase is written about a site
  that is already up.
- **That the nineteen briefs stand in for what a caller writes.** They are this
  repository's own sentences, one per intent; the forty-two that were written by
  somebody describing a task rather than guarding one are the scenario prompts.

## Wrong if

- A brief about work inside a site that already exists says "site from scratch"
  and is routed into the installation workflow, with its checklist stated as
  fact.
- A compound brief matches only the weak `development installation`, and the
  session reads "Possibly also" as an aside. A weak match carries no skill, and
  the route is what the feedback said was missing.
- A session given the corrected brief still activates
  `typo3-development-installation` by hand, or does not activate it at all. Then
  the route was not what was missing.
