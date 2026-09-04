---
date: 2026-09-03T23:53:30+00:00
category: wrong-answer
status: closed
closed: 2026-09-04
model: claude-opus-5[1m]
tool: typo3_task_guide
directory: /home/benji/projects/site-tierheim
---

# task_guide detected a site-setting intent and spent four checklist items on it while missing stat...

## Observation

Task: add an XML sitemap and a robots.txt static route to a TYPO3 v14 project site, and include the typo3/seo-sitemap set.

I called typo3_task_guide with that task text spelled out, changeType "feature", targetVersion 14, and three paths: two set files and config/sites/main/config.yaml. It came back with intents [{id: "site-setting", confidence: "strong"}] and a checklist carrying four items about adding a setting to a site set - "Decide who the value is for before deciding where it goes", "Define it and give it a value in two different places", "Say which set owns it", and a pointer to id=site-set-settings.

None of that was the task. I added no setting; I removed one before I was done. The intent's own condition text even excludes what I was doing: "only if the task adds or changes a setting a site set defines; the site configuration itself - its languages, its base, its error handling - is not this and is not covered". The guide printed that condition and then produced the checklist anyway.

Meanwhile the two facts the task actually turned on appear nowhere in the answer: that robots.txt is a routes entry in the site's own config.yaml, and that a site set cannot carry one. The user asked me twice whether it could go in the set, and I answered from typo3_hint_lookup and then from the core source, not from the guide.

My reading of the cause: the paths I passed included a Sets/ directory containing settings.definitions.yaml, and "site set" plus "settings" in the task text was enough to fire the intent - which then crowded out the subject that had the most weight in the sentence. The words "robots.txt", "static route" and "sitemap" reached nothing.

## Query

typo3_task_guide task="Add XML sitemap to a TYPO3 site: include the typo3/seo-sitemap site set, register a records sitemap provider for a custom table, and add a robots.txt route to the site configuration" changeType="feature" targetVersion="14" paths=["packages/animalshelter_sitepackage/Configuration/Sets/AnimalShelter/config.yaml","packages/animalshelter_animals/Configuration/Sets/Animals/setup.typoscript","config/sites/main/config.yaml"]

## Suggestion

Two things.

First, hold the site-setting intent against its own condition before emitting its checklist. The condition is already written and already correct; if a task names no setting and the only evidence is a Sets/ path, the intent should drop to weak or be withheld rather than contribute four items. A guide that spends a third of its checklist on the wrong subject teaches a caller to skim the rest.

Second, "robots.txt", "static route" and "routes" should reach an answer. A statement that routes is a site configuration key, resolved by the StaticRouteResolver middleware before the TYPO3 bootstrap, taking staticText, uri or asset, and that no site set can ship one, would have answered the user's question directly. Site handling in general - base, baseVariants, errorHandling, routes - seems to be a gap next to the well-covered site sets; the guide's own intent condition lists exactly those keys as "not covered", which reads like an admission rather than a boundary.
