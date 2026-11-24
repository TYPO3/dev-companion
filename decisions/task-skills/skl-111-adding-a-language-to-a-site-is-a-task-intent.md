---
id: D-SKL-111
title: Adding a language to a site is a task intent
date: 2026-11-24
status: open
coveredBy: []
---

# D-SKL-111 — Adding a language to a site is a task intent

**`knowledge/task-intents.json` has the intent `site-language`. It routes a task
that adds a language to a site to the hints that already answer it, and it names
no task skill.**

A feedback from the T3CA talk recordings of 2026-11-24 reported a stage that
added Kiswahili to a site and called this server not once. That is step 3 of the
ladder, routing.

## Evidence

- The hints were there: `user-defined-locale`, `site-language-fallback`,
  `site-label-language`, `language-files`, and `form-framework` for
  `translationFiles`.
- No intent matched the task. `site-setting` stated that the languages of a site
  configuration are not covered.
- Re-run through `TaskGuide` with the new intent, both the stage's first
  sentence and its task match `site-language`, and the brief carries
  `user-defined-locale`.

## Decided

- The checklist names each step and the hint that owns it. It states nothing
  about TYPO3 that the hints do not already state.
- The feedback's right-to-left point stays out. Nothing in the corpus states
  where the `dir` attribute comes from, and no read here established it.
- No task skill. One stage of one session is not the evidence
  `writing-a-skill.rst` asks for.

## Wrong if

- A session with the intent in its brief still reads the core for the locale
  registration. Then the brief does not deliver what the intent names.
- More sessions add a language and invent the same order. Then the domain has
  earned a skill.
