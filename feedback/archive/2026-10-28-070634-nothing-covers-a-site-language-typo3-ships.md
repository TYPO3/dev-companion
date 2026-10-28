---
date: 2026-10-28T07:06:34+00:00
category: missing-knowledge
status: closed
closed: 2026-10-28
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/lina/projects/typo3/projects/jambo-chep
---

# nothing covers a site language TYPO3 ships neither a locale nor a language pack for

## Observation

Task: translate part of a sitepackage site into Swahili, TYPO3 14.3.

site-label-language covers a non-English site whose language TYPO3 ships. It says to activate the locale and run typo3 language:update. Swahili is not one of the 65 locales the core ships. So that command answers "Language iso code sw not available or active" and can never succeed.

Four things follow, and no subject carries them. The language is registered under SYS/localization/locales/user. The configuration API refuses that path without saying so: setLocalConfigurationValueByPath returns a bool, and the path is neither whitelisted nor present in DefaultConfiguration. The regional locale is registered beside the language, because Locales looks for sw_KE before sw, and sw alone leaves every page of that language at not-found. The container needs the locale generated, or PHP's setlocale logs an error on every request of that language.

A query about this answers at 27 percent.

What the corpus does carry, it carried well. language-files holds the locale-prefixed file convention and LANG/resourceOverrides. I received that hint and read a window of it, so I used neither. That half is mine rather than the corpus's.

## Query

typo3_hint_lookup task='add a site language TYPO3 does not ship a locale or a language pack for, and translate the labels of a third party extension for it' targetVersion=14. Earlier: ids=[site-label-language, language-files, translation-domain].

## Suggestion

Add what a language outside the shipped 65 requires. Name SYS/localization/locales/user, and say that both the language and the regional locale are registered. Say that the configuration API will not write that path and reports nothing. Say that the operating system locale is a step of its own.

Say in site-label-language which half of it assumes a pack exists. A reader on a language with no pack follows it to a command that cannot work.
