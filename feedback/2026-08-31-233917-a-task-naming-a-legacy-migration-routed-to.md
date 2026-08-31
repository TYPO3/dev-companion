---
date: 2026-08-31T23:39:17+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3-development-installation, typo3-content-element-development
directory: /home/benji/projects/site-tierheim
---

# A task naming a legacy migration routed to installation, never to content modelling

## Observation

Task, as the user gave it at 18:38:34: "wir wollen http://www.tierheim-wiesbaden.de/ relaunchen auf TYPO3 basis, wir brauchen eine typo3 v14 mit neuem frontend und wollen die bisherigen inhalte übernehmen".

The user's own diagnosis at the end of the session, which is better than mine and is what this feedback is about: "auch beim setup war es schwierig, du hast dich viel damit beschäftigt wie die daten aus dem anderen system kommen und nicht wie sie gut hier gepflegt werden würden."

He is right, and it explains the session's worst failure at its root. Starting from "how does the content come out of Drupal 6" leads to one blob per page, and the core `html` CType is where a blob naturally lands. Starting from "how will an editor maintain this" leads to typed elements first, and the import becomes a mapping into them. I did the first. Six hours and five complaints later I did the second, by rewriting everything.

What the routing did, exactly:

18:44:25 Skill(typo3-development-installation), args "Neues TYPO3 v14 Projekt (Sitepackage + eigene Extension) von Grund auf mit DDEV aufsetzen".
18:44:41 typo3_task_guide, task "Set up a new TYPO3 v14 project from scratch with DDEV: Composer root package, sitepackage extension with Fluid templates and site set, and a custom extension with TCA records for animal listings. Migrate content from a legacy Drupal 6 site.", changeType "operations".

My task text named the migration. The answer came back scoped to installation and operations. Nothing in it, and nothing in the installation skill, said that a task carrying "migrate content from a legacy site" has a content-modelling question in front of it that decides everything downstream. typo3-content-element-development — whose first two headings are "Choose the content model first" and "Describe how an editor creates, orders, translates, hides and deletes the content before implementing it" — was not named, and did not activate until 21:30, twelve seconds after the user told me to read up.

By then I had written a 1300-line migration command, seven Python scraper scripts, and imported 136 pages of markup into html CTypes. The content model was then reverse-engineered out of scraped output rather than designed, which is why it took five more corrections to land.

The two workflows were both present and correctly written. What was missing was anything that reads "relaunch an existing site onto TYPO3" as a task where the modelling comes first and the installation is scaffolding.

## Query

typo3_task_guide(task="Set up a new TYPO3 v14 project from scratch with DDEV: Composer root package, sitepackage extension with Fluid templates and site set, and a custom extension with TCA records for animal listings. Migrate content from a legacy Drupal 6 site.", changeType="operations", targetVersion="14", paths=["composer.json",".ddev/config.yaml","packages/tierheim_sitepackage","packages/tierheim_animals"]) at 18:44:41. Skill(typo3-development-installation) at 18:44:25. Skill(typo3-content-element-development) not until 21:30:15.

## Suggestion

Make the task text "migrate" or "relaunch" or "legacy" reroute rather than pass through.

When typo3_task_guide sees a task that carries an existing body of content moving into TYPO3, the brief should open with the modelling question and name typo3-content-element-development as the workflow that comes first — before the installation workflow, not beside it. Something like: "This task carries existing content. What the content elements are is decided before the import is written, because an import writes whatever model it is given and a blob is a model. Load typo3-content-element-development first; typo3-development-installation is the scaffolding it lands in."

A hint would carry it too, findable from the words such a session actually uses. Suggested id: legacy-content-relaunch. Statements worth having in it:

- The import is the last step, not the first. What the typed elements are is decided from the target's editing experience, not from the source's markup.
- One content element per section of a legacy page, not one per page. A page that arrives as a single element has not been migrated, it has been pasted.
- The core `html` CType is not a migration target. It hands unfiltered markup to the visitor and belongs in no editor's selector; a migration that produces it has skipped the modelling step.
- What the source cannot express — a structured team list, a fee table, a bullet list with links — is exactly where the new model is decided, and is worth reading the legacy pages for before any crawler is written.

That last point is the one that would have changed this session: I read the legacy pages to write a scraper. Reading them to find the six recurring shapes would have produced the same six content elements I eventually built, on day one.
