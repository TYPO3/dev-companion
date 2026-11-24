---
date: 2026-11-24T12:44:30+00:00
category: tool-gap
status: closed
closed: 2026-11-24
model: claude-opus-5-5
tool: typo3_task_guide
---

# no task intent routes adding a site language, so a Kiswahili session never called the server

## Observation

Debrief of the T3CA 2026 talk recordings (a TYPO3 14.3 site package built by Claude Code with this server as a Composer dev dependency, stages 01 to 14).

Stage 14 started with "we still need the language menu" and added Kiswahili as a second site language, translated pages, news, the contact form and its labels. The session called this server not once. It read vendor/typo3 21 times instead: Locales.php to learn that TYPO3 does not list sw and how a language is registered, and cms-form's TranslationService, Form.yaml and the finishers to find translationFiles.

The knowledge was there. user-defined-locale answers the registration word for word (locales.user in additional.php, configuration:set failing on that path), and site-language-fallback, language-files and site-label-language cover the rest. Nothing led the session to them: task-intents.json has no intent for adding a site language, and the site-set intent states that the site configuration's languages are not covered.

Every other stage that built something began with typo3_project_describe and typo3_task_guide. This one is the only stage with a build and no call. That the missing intent is the reason is a reading of the transcript, not something the session said.

## Query

we still need the language menu (then: add Kiswahili as a second language, translate the pages and the contact form)

## Suggestion

A task intent for adding a language to a site: the language in config/sites, a language TYPO3 does not list (user-defined-locale), labels without a pack (site-label-language, language-files), fallback (site-language-fallback), translated records, EXT:form translationFiles, and a right-to-left language (the dir attribute comes from the locale, the site's CSS has to use logical properties).
