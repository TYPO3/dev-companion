---
date: 2026-10-28T13:32:42+00:00
category: missing-knowledge
status: closed
closed: 2026-10-29
model: claude-opus-5
tool: typo3_task_guide, typo3_hint_lookup, typo3_ter_lookup
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# Nothing asks whether an extension already does this, before a feature is built

## Observation

The task was to turn a block of notice prose on the home page of a TYPO3 14.3 project site into separate dated news items, three on the home page and the rest on an archive page.

I built an own TCA record table for it, with a content element and a data processor. It works. The person I was working with then asked why I had not used georgringer/news or pages, and only at that point did I call typo3_ter_lookup, which answered in one call: news 14.1.2, stable, uploaded 2026-10-12, TYPO3 13 and 14, >=13.4.29 <=14.99.99. The extension was current, supported the target version, and natively covers the one shape I had treated as the reason not to use it - a headline that links elsewhere instead of having a detail view, which is its "Internal page" and "External URL" news types.

So the decision was taken without the fact that most bears on it, and the server could have supplied that fact at any point before the first file was written.

Two things in the server leave it that way.

The question is never raised. typo3_task_guide briefed this task - domains, hints, checks, a checklist - and nothing in it asks whether something already does this. The server's own "What to call for what" list covers backend markup, icon identifiers, labels, changelog entries, commit messages and whole procedures, and says nothing about the ecosystem. typo3_hint_lookup for "choose an existing third-party extension instead of building your own, and check whether it supports this TYPO3 version" answers "No hint matched", and the nearest ids it offers - extension-repository-layout, extension-manifest, extension-ter-release - are all about publishing or laying out an extension of one's own.

The tool that answers half of it cannot be asked the other half. typo3_ter_lookup takes an extension key. It cannot be asked what exists for news, for a calendar, for cookie consent, so it answers "is it available for this version" only once you already know which extension to name - which is the part a model is least reliable at, because it is recall about a moving ecosystem rather than a lookup.

What makes this worth a hint rather than a note is that the cost is asymmetric and front-loaded. Switching cost five records on the day I built it; it rises with every record and every shared URL, and the site carries a bespoke table a maintainer has to learn instead of a documented one. The decision is cheap to get right before the first file and expensive afterwards, which is exactly where a brief earns its place.

## Query

typo3_hint_lookup task="choose an existing third-party extension instead of building your own, and check whether it supports this TYPO3 version"; typo3_ter_lookup extension=news

## Suggestion

A hint that fires on building a feature, saying to name the extension that already covers it and to establish its support for the target version with typo3_ter_lookup before writing code - and saying what that answer settles and what it does not, because a TER range is a declaration by the author rather than evidence the release was tested there.

It should carry the counter-weight too, or it becomes "always take the extension": a dependency couples the site's upgrade to somebody else's release schedule, and an extension whose surface is mostly unused is surface a maintainer still has to read. The judgement is between those, and a hint that states both is one a model can actually apply.

typo3_task_guide is where it belongs as a line in the brief for a change that adds a feature, the way the deprecation sweep is already a line there.

For the discovery half, either the corpus names the widely used extensions by subject - news, calendar, forms, search, containers, consent - or typo3_ter_lookup takes a subject as well as a key. Without one of those, the check can only confirm a name a model already guessed, and the guess is the weak step.
